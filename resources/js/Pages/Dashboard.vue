<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TaskList from '@/Components/Tasks/List.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({ tasks: Array, priorities: Array })
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <TaskList :tasks="tasks" :priorities="priorities" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
