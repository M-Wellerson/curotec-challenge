<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CategoryManager from '@/Components/Categories/Manager.vue';
import TaskList from '@/Components/Tasks/List.vue';
import {Head} from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.css';

const props = defineProps({
    tasks: Array,
    priorities: Array,
    statuses: Array,
    categories: Array,
})

</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <CategoryManager :categories="categories"/>
                <TaskList :tasks="tasks" :priorities="priorities.data" :statuses="statuses.data" :categories="categories.data"/>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
