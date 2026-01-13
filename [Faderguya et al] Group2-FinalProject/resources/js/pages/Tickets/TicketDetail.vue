<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { ref, onMounted, computed } from 'vue';
import axios from '@/lib/axios';
import { usePage } from '@inertiajs/vue3';

const props = defineProps<{
    ticketId: string | number
}>();

const page = usePage();
const ticket = ref<any>(null);
const loading = ref(true);
const commentContent = ref('');
const submittingComment = ref(false);

// Get current user from page props (Inertia shares this automatically)
const user = computed(() => page.props.auth.user);

const isAdmin = computed(() => user.value?.role === 'admin');
const isFaculty = computed(() => user.value?.role === 'faculty');
const isCreator = computed(() => ticket.value?.user_id === user.value?.id);
const canEdit = computed(() => {
    if (!ticket.value) return false;
    return isAdmin.value || 
           (isCreator.value && ticket.value.status === 'Open') ||
           (isFaculty.value && ticket.value.assigned_to === user.value?.id);
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: '/tickets',
    },
    {
        title: `Ticket #${props.ticketId}`,
        href: `/tickets/${props.ticketId}`,
    },
];

const fetchTicket = async () => {
    try {
        loading.value = true;
        console.log('Fetching ticket:', props.ticketId);
        const response = await axios.get(`/api/tickets/${props.ticketId}`);
        ticket.value = response.data;
    } catch (error: any) {
        console.error('Error fetching ticket:', error);
        console.error('Status:', error.response?.status);
        console.error('Response:', error.response?.data);
        
        if (error.response?.status === 404) {
            alert('Ticket not found or you do not have permission to view it.');
        }
    } finally {
        loading.value = false;
    }
};

const addComment = async () => {
    if (!commentContent.value.trim()) return;
    
    try {
        submittingComment.value = true;
        await axios.post(`/api/tickets/${props.ticketId}/comments`, {
            content: commentContent.value
        });
        commentContent.value = '';
        await fetchTicket();
    } catch (error: any) {
        console.error('Error adding comment:', error);
        console.error('Validation errors:', error.response?.data?.errors);
        alert(`Error: ${error.response?.data?.message || 'Failed to add comment'}`);
    } finally {
        submittingComment.value = false;
    }
};

const updateTicket = async (field: string, value: any) => {
    try {
        await axios.patch(`/api/tickets/${props.ticketId}`, {
            [field]: value
        });
        await fetchTicket();
    } catch (error) {
        console.error('Error updating ticket:', error);
    }
};

const deleteTicket = async () => {
    if (!confirm('Are you sure you want to delete this ticket?')) return;
    
    try {
        await axios.delete(`/api/tickets/${props.ticketId}`);
        router.visit('/tickets');
    } catch (error) {
        console.error('Error deleting ticket:', error);
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
    fetchTicket();
});
</script>

<template>
    <Head :title="`Ticket #${ticketId}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div v-if="loading" class="flex items-center justify-center py-12">
            <p class="text-muted-foreground">Loading ticket...</p>
        </div>

        <div v-else-if="ticket" class="p-6 space-y-6">
            <!-- Ticket Header -->
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h1 class="text-3xl font-bold">{{ ticket.subject }}</h1>
                        <Badge :variant="getStatusVariant(ticket.status)">{{ ticket.status }}</Badge>
                    </div>
                    <p class="text-muted-foreground">
                        Created by {{ ticket.creator?.name }} on {{ new Date(ticket.created_at).toLocaleDateString() }}
                    </p>
                </div>
                
                <div class="flex gap-2">
                    <Button v-if="isAdmin" variant="destructive" @click="deleteTicket">Delete</Button>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <!-- Main Content -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Description -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Description</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p class="whitespace-pre-wrap">{{ ticket.description }}</p>
                            
                            <!-- Attachment -->
                            <div v-if="ticket.attachment" class="mt-4 pt-4 border-t">
                                <p class="text-sm font-semibold mb-2">Attachment:</p>
                                <a 
                                    :href="`/storage/${ticket.attachment}`" 
                                    target="_blank"
                                    class="inline-block"
                                >
                                    <img 
                                        :src="`/storage/${ticket.attachment}`" 
                                        alt="Ticket attachment"
                                        class="max-w-md rounded-lg border hover:opacity-90 transition-opacity cursor-pointer"
                                    />
                                </a>
                                <p class="text-xs text-muted-foreground mt-1">Click to view full size</p>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Comments -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Comments</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div v-if="ticket.comments && ticket.comments.length > 0">
                                <div 
                                    v-for="comment in ticket.comments" 
                                    :key="comment.id"
                                    class="p-4 border rounded-lg space-y-2"
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="font-semibold">{{ comment.user?.name }}</span>
                                        <span class="text-xs text-muted-foreground">
                                            {{ new Date(comment.created_at).toLocaleString() }}
                                        </span>
                                    </div>
                                    <p class="text-sm whitespace-pre-wrap">{{ comment.content }}</p>
                                </div>
                            </div>
                            <p v-else class="text-muted-foreground text-center py-4">No comments yet</p>

                            <!-- Add Comment -->
                            <div class="space-y-2 pt-4 border-t">
                                <Label>Add Comment</Label>
                                <Textarea 
                                    v-model="commentContent" 
                                    placeholder="Write your comment..."
                                    rows="3"
                                />
                                <Button 
                                    @click="addComment" 
                                    :disabled="!commentContent.trim() || submittingComment"
                                    class="w-full"
                                >
                                    {{ submittingComment ? 'Posting...' : 'Post Comment' }}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-4">
                    <!-- Priority -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Priority</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Select 
                                v-if="canEdit"
                                :model-value="ticket.priority"
                                @update:model-value="(val) => updateTicket('priority', val)"
                            >
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Low">Low</SelectItem>
                                    <SelectItem value="Medium">Medium</SelectItem>
                                    <SelectItem value="High">High</SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-else class="text-lg font-semibold">{{ ticket.priority }}</p>
                        </CardContent>
                    </Card>

                    <!-- Status -->
                    <Card v-if="isAdmin || isFaculty">
                        <CardHeader>
                            <CardTitle>Status</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Select 
                                :model-value="ticket.status"
                                @update:model-value="(val) => updateTicket('status', val)"
                            >
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Open">Open</SelectItem>
                                    <SelectItem value="In Progress">In Progress</SelectItem>
                                    <SelectItem value="Resolved">Resolved</SelectItem>
                                    <SelectItem value="Closed">Closed</SelectItem>
                                </SelectContent>
                            </Select>
                        </CardContent>
                    </Card>

                    <!-- Assigned To -->
                    <Card v-if="ticket.assigned_to">
                        <CardHeader>
                            <CardTitle>Assigned To</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p>{{ ticket.assignedTo?.name || 'Unassigned' }}</p>
                            <p class="text-xs text-muted-foreground">{{ ticket.assignedTo?.role }}</p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <div v-else class="flex flex-col items-center justify-center py-12">
            <p class="text-muted-foreground">Ticket not found</p>
            <Button @click="router.visit('/tickets')" class="mt-4">Back to Tickets</Button>
        </div>
    </AppLayout>
</template>
