<template>
    <div class="space-y-6">
        <!-- create task -->
        <form @submit.prevent="submit" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <input
                v-model="form.title"
                type="text"
                placeholder="Task title"
                class="flex-1 rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                required
            />

            <select
                v-model.number="form.priority_id"
                class="rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                required
            >
                <option disabled value="">Priority</option>
                <option v-for="p in priorities" :key="p.id" :value="p.id">
                    {{ p.name }}
                </option>
            </select>

            <input
                v-model="form.due_date"
                type="date"
                class="rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
            />

            <button
                type="submit"
                :disabled="form.processing || !form.title.trim() || !form.priority_id"
                class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
            >
                Add
            </button>
        </form>

        <!-- list tasks -->
        <ul>
            <li
                v-for="task in tasks"
                :key="task.id"
                class="mb-2 flex items-center justify-between rounded border border-gray-200 bg-white p-3 shadow-sm"
            >
                <div class="flex flex-col">
                    <span class="font-medium">{{ task.title }}</span>
                    <span class="text-xs text-gray-500">Due: {{ task.due_date ? new Date(task.due_date).toLocaleDateString() : '—' }} • {{ task.priority?.name }}</span>
                </div>
                <button
                    @click="destroy(task.id)"
                    class="text-sm font-medium text-red-600 hover:text-red-800"
                >
                    Delete
                </button>
            </li>
            <li v-if="!tasks.length" class="text-sm text-gray-500">No tasks yet.</li>
        </ul>
    </div>
</template>

<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    tasks: { type: Array, required: true },
    priorities: { type: Array, required: true },
});

const form = useForm({
    title: '',
    priority_id: '',
    due_date: '',
});

function submit() {
    form.post(route('tasks.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('title', 'priority_id', 'due_date'),
    });
}

function destroy(id) {
    router.delete(route('tasks.destroy', id), { preserveScroll: true });
}

const priorities = computed(() => props.priorities);
const tasks = computed(() => props.tasks);
</script>
