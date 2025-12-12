<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { ref, onMounted, computed } from 'vue';
import axios from '@/lib/axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const tickets = ref([]);
const loading = ref(true);

const user = computed(() => (window as any).$page?.props?.auth?.user);

const stats = computed(() => {
    return {
        total: tickets.value.length,
        open: tickets.value.filter((t: any) => t.status === 'Open').length,
        inProgress: tickets.value.filter((t: any) => t.status === 'In Progress').length,
        resolved: tickets.value.filter((t: any) => t.status === 'Resolved').length,
    };
});

const recentTickets = computed(() => {
    return tickets.value.slice(0, 5);
});

const fetchTickets = async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/tickets');
        tickets.value = response.data;
    } catch (error) {
        console.error('Error fetching tickets:', error);
    } finally {
        loading.value = false;
    }
};

const getStatusVariant = (status: string) => {
    const variants: Record<string, string> = {
        'Open': 'default',
        'In Progress': 'secondary',
        'Resolved': 'outline',
        'Closed': 'destructive'
    };
    return variants[status] || 'default';
};

onMounted(() => {
    fetchTickets();
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Welcome Section -->
            <div>
                <h1 class="text-3xl font-bold">Welcome back, {{ user?.name }}!</h1>
                <p class="text-muted-foreground">Here's an overview of your support tickets</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Total Tickets</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Open</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">{{ stats.open }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">In Progress</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-yellow-600">{{ stats.inProgress }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Resolved</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-green-600">{{ stats.resolved }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Recent Tickets -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Recent Tickets</CardTitle>
                            <CardDescription>Your most recent support requests</CardDescription>
                        </div>
                        <Button @click="router.visit('/tickets')">View All</Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="loading" class="text-center py-8 text-muted-foreground">
                        Loading tickets...
                    </div>

                    <div v-else-if="recentTickets.length === 0" class="text-center py-8">
                        <p class="text-muted-foreground mb-4">No tickets yet</p>
                        <Button @click="router.visit('/tickets')">Create Your First Ticket</Button>
                    </div>

                    <div v-else class="space-y-4">
                        <div 
                            v-for="ticket in recentTickets" 
                            :key="ticket.id"
                            @click="router.visit(`/tickets/${ticket.id}`)"
                            class="flex items-center justify-between p-4 border rounded-lg hover:bg-accent cursor-pointer transition-colors"
                        >
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold">{{ ticket.subject }}</h4>
                                    <Badge :variant="getStatusVariant(ticket.status)">{{ ticket.status }}</Badge>
                                </div>
                                <p class="text-sm text-muted-foreground">
                                    {{ ticket.priority }} Priority • {{ new Date(ticket.created_at).toLocaleDateString() }}
                                </p>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Quick Actions -->
            <Card>
                <CardHeader>
                    <CardTitle>Quick Actions</CardTitle>
                    <CardDescription>Common tasks you can perform</CardDescription>
                </CardHeader>
                <CardContent class="flex gap-4">
                    <Button @click="router.visit('/tickets')">Create New Ticket</Button>
                    <Button variant="outline" @click="router.visit('/tickets')">View All Tickets</Button>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
