<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ref, onMounted } from 'vue';
import axios from '@/lib/axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: '/admin/dashboard',
    },
    {
        title: 'User Management',
        href: '/admin/users',
    },
];

const users = ref([]);
const loading = ref(true);
const currentUser = ref<any>(null);

const fetchUsers = async () => {
    try {
        loading.value = true;
        // Get current user
        currentUser.value = (window as any).$page?.props?.auth?.user;
        
        // Fetch all users from API
        const response = await axios.get('/api/admin/users');
        users.value = Array.isArray(response.data) ? response.data : [];
    } catch (error) {
        console.error('Error fetching users:', error);
        users.value = [];
    } finally {
        loading.value = false;
    }
};

const updateUserRole = async (userId: number, newRole: string) => {
    try {
        await axios.patch(`/api/admin/users/${userId}/role`, {
            role: newRole
        });
        
        // Update local state
        const user = users.value.find((u: any) => u.id === userId);
        if (user) {
            (user as any).role = newRole;
        }
    } catch (error: any) {
        alert(error.response?.data?.message || 'Error updating user role');
        console.error('Error updating role:', error);
    }
};

const getRoleBadgeVariant = (role: string) => {
    const variants: Record<string, string> = {
        'admin': 'destructive',
        'faculty': 'secondary',
        'student': 'default'
    };
    return variants[role] || 'default';
};

onMounted(() => {
    fetchUsers();
});
</script>

<template>
    <Head title="User Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold">User Management</h1>
                <p class="text-muted-foreground">Manage user roles and permissions</p>
            </div>

            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Total Users</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ users.length }}</div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Admins</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-600">
                            {{ users.filter((u: any) => u.role === 'admin').length }}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-medium text-muted-foreground">Students</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ users.filter((u: any) => u.role === 'student').length }}
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Users List -->
            <Card>
                <CardHeader>
                    <CardTitle>All Users</CardTitle>
                    <CardDescription>Manage user roles and access levels</CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="loading" class="text-center py-8 text-muted-foreground">
                        Loading users...
                    </div>

                    <div v-else-if="users.length === 0" class="text-center py-8">
                        <p class="text-muted-foreground">No users found</p>
                    </div>

                    <div v-else class="space-y-3">
                        <div 
                            v-for="user in users" 
                            :key="user.id"
                            class="flex items-center justify-between p-4 border rounded-lg"
                        >
                            <div class="flex-1 space-y-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-semibold">{{ user.name }}</h4>
                                    <Badge :variant="getRoleBadgeVariant(user.role)">
                                        {{ user.role }}
                                    </Badge>
                                    <span v-if="currentUser?.id === user.id" class="text-xs text-muted-foreground">
                                        (You)
                                    </span>
                                </div>
                                <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                            </div>

                            <div class="w-40">
                                <Select 
                                    :model-value="user.role"
                                    @update:model-value="(val) => updateUserRole(user.id, val)"
                                >
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="student">Student</SelectItem>
                                        <SelectItem value="faculty">Faculty</SelectItem>
                                        <SelectItem value="admin">Admin</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Info Card -->
            <Card>
                <CardHeader>
                    <CardTitle>Role Permissions</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div class="flex items-start gap-2">
                        <Badge variant="destructive" class="mt-1">Admin</Badge>
                        <p class="text-sm text-muted-foreground">
                            Full access to all features. Can manage users, delete tickets, and view all data.
                        </p>
                    </div>
                    <div class="flex items-start gap-2">
                        <Badge variant="secondary" class="mt-1">Faculty</Badge>
                        <p class="text-sm text-muted-foreground">
                            Can view assigned tickets, update ticket status, and manage their own tickets.
                        </p>
                    </div>
                    <div class="flex items-start gap-2">
                        <Badge variant="default" class="mt-1">Student</Badge>
                        <p class="text-sm text-muted-foreground">
                            Can create tickets, view their own tickets, and edit them while status is "Open".
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
