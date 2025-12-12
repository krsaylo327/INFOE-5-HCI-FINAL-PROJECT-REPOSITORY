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
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
];

const tickets = ref([]);
const loading = ref(true);

const stats = computed(() => {
    return {
        total: tickets.value.length,
        open: tickets.value.filter((t: any) => t.status === 'Open').length,
        inProgress: tickets.value.filter((t: any) => t.status === 'In Progress').length,
        resolved: tickets.value.filter((t: any) => t.status === 'Resolved').length,
        highPriority: tickets.value.filter((t: any) => t.priority === 'High').length,
    };
});

const recentTickets = computed(() => {
    return tickets.value.slice(0, 10);
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

const getPriorityColor = (priority: string) => {
    const colors: Record<string, string> = {
        'High': 'text-red-500',
        'Medium': 'text-yellow-500',
        'Low': 'text-green-500'
    };
    return colors[priority] || '';
};

onMounted(() => {
    fetchTickets();
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Welcome Section -->
            <div>
                <h1 class="text-3xl font-bold">Admin Dashboard</h1>
                <p class="text-muted-foreground">Manage and oversee all support tickets</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid gap-4 md:grid-cols-5">
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

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">High Priority</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">{{ stats.highPriority }}</div>
                    </CardContent>
                </Card>
            </div>

            <!-- Quick Actions -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card class="hover:bg-accent cursor-pointer transition-colors" @click="router.visit('/tickets')">
                    <CardHeader>
                        <CardTitle>All Tickets</CardTitle>
                        <CardDescription>View and manage all support tickets</CardDescription>
                    </CardHeader>
                </Card>

                <Card class="hover:bg-accent cursor-pointer transition-colors" @click="router.visit('/admin/users')">
                    <CardHeader>
                        <CardTitle>User Management</CardTitle>
                        <CardDescription>Manage user roles and permissions</CardDescription>
                    </CardHeader>
                </Card>

                <Card class="hover:bg-accent cursor-pointer transition-colors" @click="router.visit('/tickets')">
                    <CardHeader>
                        <CardTitle>Create Ticket</CardTitle>
                        <CardDescription>Create a new support ticket</CardDescription>
                    </CardHeader>
                </Card>
            </div>

            <!-- All Tickets Table -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>All Tickets</CardTitle>
                            <CardDescription>Complete list of all support tickets</CardDescription>
                        </div>
                        <Button @click="router.visit('/tickets')">Manage All</Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="loading" class="text-center py-8 text-muted-foreground">
                        Loading tickets...
                    </div>

                    <div v-else-if="recentTickets.length === 0" class="text-center py-8">
                        <p class="text-muted-foreground">No tickets found</p>
                    </div>

                    <div v-else class="space-y-3">
                        <div 
                            v-for="ticket in recentTickets" 
                            :key="ticket.id"
                            @click="router.visit(`/tickets/${ticket.id}`)"
                            class="flex items-center justify-between p-4 border rounded-lg hover:bg-accent cursor-pointer transition-colors"
                        >
                            <div class="flex-1 space-y-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-semibold">{{ ticket.subject }}</h4>
                                    <Badge :variant="getStatusVariant(ticket.status)">{{ ticket.status }}</Badge>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-muted-foreground">
                                    <span>By {{ ticket.creator?.name }}</span>
                                    <span>•</span>
                                    <span :class="getPriorityColor(ticket.priority)">{{ ticket.priority }}</span>
                                    <span>•</span>
                                    <span>{{ new Date(ticket.created_at).toLocaleDateString() }}</span>
                                    <span v-if="ticket.assigned_to">•</span>
                                    <span v-if="ticket.assigned_to">Assigned to {{ ticket.assignedTo?.name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
