<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ref, onMounted, computed } from 'vue';
import axios from '@/lib/axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: '/tickets',
    },
];

const tickets = ref([]);
const loading = ref(true);
const showCreateDialog = ref(false);
const searchQuery = ref('');
const statusFilter = ref('all');
const priorityFilter = ref('all');
const selectedFile = ref<File | null>(null);

const newTicket = ref({
    subject: '',
    description: '',
    priority: 'Low'
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        selectedFile.value = target.files[0];
    }
};

// Computed filtered and sorted tickets
const filteredTickets = computed(() => {
    let result = [...tickets.value];
    
    // Search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter((ticket: any) => 
            ticket.subject.toLowerCase().includes(query) ||
            ticket.description.toLowerCase().includes(query) ||
            ticket.creator?.name.toLowerCase().includes(query)
        );
    }
    
    // Status filter
    if (statusFilter.value !== 'all') {
        result = result.filter((ticket: any) => ticket.status === statusFilter.value);
    }
    
    // Priority filter
    if (priorityFilter.value !== 'all') {
        result = result.filter((ticket: any) => ticket.priority === priorityFilter.value);
    }
    
    // Sort: 1) Status (Closed last), 2) Priority (High first), 3) Date (newest first)
    const priorityOrder: Record<string, number> = { 'High': 3, 'Medium': 2, 'Low': 1 };
    const statusOrder: Record<string, number> = { 
        'Open': 4, 
        'In Progress': 3, 
        'Resolved': 2, 
        'Closed': 1  // Closed tickets go to bottom
    };
    
    result.sort((a: any, b: any) => {
        // First sort by status (Closed goes to bottom)
        const statusDiff = (statusOrder[b.status] || 0) - (statusOrder[a.status] || 0);
        if (statusDiff !== 0) return statusDiff;
        
        // Then by priority (High first)
        const priorityDiff = (priorityOrder[b.priority] || 0) - (priorityOrder[a.priority] || 0);
        if (priorityDiff !== 0) return priorityDiff;
        
        // Finally by date (newest first)
        return new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
    });
    
    return result;
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

const createTicket = async () => {
    try {
        const formData = new FormData();
        formData.append('subject', newTicket.value.subject);
        formData.append('description', newTicket.value.description);
        formData.append('priority', newTicket.value.priority);
        
        if (selectedFile.value) {
            formData.append('attachment', selectedFile.value);
        }
        
        await axios.post('/api/tickets', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        showCreateDialog.value = false;
        newTicket.value = { subject: '', description: '', priority: 'Low' };
        selectedFile.value = null;
        await fetchTickets();
    } catch (error: any) {
        console.error('Error creating ticket:', error);
        console.error('Error response:', error.response?.data);
        alert(`Error: ${error.response?.data?.message || 'Failed to create ticket'}`);
    }
};

const viewTicket = (id: number) => {
    router.visit(`/tickets/${id}`);
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
    <Head title="Tickets" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Support Tickets</h1>
                    <p class="text-muted-foreground">Manage and track your support requests</p>
                </div>
                
                <Dialog v-model:open="showCreateDialog">
                    <DialogTrigger as-child>
                        <Button>Create Ticket</Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Create New Ticket</DialogTitle>
                            <DialogDescription>
                                Fill out the form below to submit a new support ticket.
                            </DialogDescription>
                        </DialogHeader>
                        <form @submit.prevent="createTicket" class="space-y-4">
                            <div class="space-y-2">
                                <Label for="subject">Subject</Label>
                                <Input id="subject" v-model="newTicket.subject" placeholder="Brief description of the issue" required />
                            </div>
                            <div class="space-y-2">
                                <Label for="description">Description</Label>
                                <Textarea id="description" v-model="newTicket.description" placeholder="Detailed explanation" required rows="4" />
                            </div>
                            <div class="space-y-2">
                                <Label for="priority">Priority</Label>
                                <Select v-model="newTicket.priority">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select priority" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Low">Low</SelectItem>
                                        <SelectItem value="Medium">Medium</SelectItem>
                                        <SelectItem value="High">High</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label for="attachment">Attachment (Optional)</Label>
                                <Input 
                                    id="attachment" 
                                    type="file" 
                                    accept="image/*"
                                    @change="handleFileChange"
                                    class="cursor-pointer"
                                />
                                <p class="text-xs text-muted-foreground">Upload a screenshot or photo (Max 2MB, JPG/PNG/GIF)</p>
                            </div>
                            <div class="flex justify-end gap-2">
                                <Button type="button" variant="outline" @click="showCreateDialog = false">Cancel</Button>
                                <Button type="submit">Create</Button>
                            </div>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <!-- Search and Filters -->
            <div class="flex gap-4 items-end">
                <div class="flex-1">
                    <Label for="search">Search</Label>
                    <Input 
                        id="search" 
                        v-model="searchQuery" 
                        placeholder="Search by subject, description, or creator..." 
                        class="mt-1"
                    />
                </div>
                <div class="w-48">
                    <Label for="status-filter">Status</Label>
                    <Select v-model="statusFilter">
                        <SelectTrigger id="status-filter" class="mt-1">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Status</SelectItem>
                            <SelectItem value="Open">Open</SelectItem>
                            <SelectItem value="In Progress">In Progress</SelectItem>
                            <SelectItem value="Resolved">Resolved</SelectItem>
                            <SelectItem value="Closed">Closed</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="w-48">
                    <Label for="priority-filter">Priority</Label>
                    <Select v-model="priorityFilter">
                        <SelectTrigger id="priority-filter" class="mt-1">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Priorities</SelectItem>
                            <SelectItem value="High">High</SelectItem>
                            <SelectItem value="Medium">Medium</SelectItem>
                            <SelectItem value="Low">Low</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <!-- Results Count -->
            <div class="text-sm text-muted-foreground">
                Showing {{ filteredTickets.length }} of {{ tickets.length }} ticket(s)
            </div>

            <!-- Tickets List -->
            <div v-if="loading" class="flex items-center justify-center py-12">
                <p class="text-muted-foreground">Loading tickets...</p>
            </div>

            <div v-else-if="tickets.length === 0" class="flex flex-col items-center justify-center py-12 border border-dashed rounded-lg">
                <p class="text-muted-foreground mb-4">No tickets yet</p>
                <Button @click="showCreateDialog = true">Create Your First Ticket</Button>
            </div>

            <div v-else-if="filteredTickets.length === 0" class="flex flex-col items-center justify-center py-12 border border-dashed rounded-lg">
                <p class="text-muted-foreground mb-4">No tickets match your filters</p>
                <Button variant="outline" @click="searchQuery = ''; statusFilter = 'all'; priorityFilter = 'all'">Clear Filters</Button>
            </div>

            <div v-else class="grid gap-4">
                <div 
                    v-for="ticket in filteredTickets" 
                    :key="ticket.id"
                    @click="viewTicket(ticket.id)"
                    class="p-4 border rounded-lg hover:bg-accent cursor-pointer transition-colors"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 space-y-1">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold">{{ ticket.subject }}</h3>
                                <Badge :variant="getStatusVariant(ticket.status)">{{ ticket.status }}</Badge>
                                <Badge v-if="ticket.priority === 'High'" variant="destructive" class="text-xs">High Priority</Badge>
                            </div>
                            <p class="text-sm text-muted-foreground line-clamp-2">{{ ticket.description }}</p>
                            <div class="flex items-center gap-4 text-xs text-muted-foreground">
                                <span>Created by {{ ticket.creator?.name || 'Unknown' }}</span>
                                <span>•</span>
                                <span :class="getPriorityColor(ticket.priority)">{{ ticket.priority }} Priority</span>
                                <span>•</span>
                                <span>{{ new Date(ticket.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
