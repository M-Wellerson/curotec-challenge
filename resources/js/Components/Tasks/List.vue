<template>
    <div class="space-y-6 relative">
        <!-- create task -->
        <form @submit.prevent="submit" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <input
                v-model="form.title"
                type="text"
                placeholder="Task title"
                class="flex-1 rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                required
            />

            <multiselect
                v-model="form.categories"
                :options="categories"
                :multiple="true"
                :close-on-select="false"
                track-by="id"
                label="name"
                placeholder="Select categories"
                class="w-full sm:max-w-md"
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

            <select
                v-model.number="form.status_id"
                class="rounded border border-gray-300 px-3 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                required
            >
                <option disabled value="">Status</option>
                <option v-for="s in statuses" :key="s.id" :value="s.id">
                    {{ s.name }}
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

        <div>
            <Multiselect
                v-model="selectedCategoryObjs"
                :options="categories"
                multiple
                track-by="id"
                label="name"
                placeholder="Categories"
            />

            <Multiselect
                v-model="selectedStatusObjs"
                :options="statuses"
                multiple
                track-by="id"
                label="name"
                placeholder="Status"
            />
            <input type="date" v-model="filter.from"/>
            <input type="date" v-model="filter.to"/>
            <button v-if="filter.hasFilters" @click="filter.$reset()" class="text-sm text-gray-600">Clear</button>
        </div>

        <!-- list tasks -->
        <ul>
            <li
                v-for="task in tasksData" :key="task.id"
                class="mb-2 rounded border border-gray-200 bg-white p-3 shadow-sm"
            >
                <div class="flex justify-between">
                    <div>
                        <p class="font-medium">{{ task.title }}</p>
                        <p class="text-xs text-gray-500">
                            Due: {{ task.due_date ? new Date(task.due_date).toLocaleDateString() : '—' }} •
                            {{ task.priority?.name || '—' }} • {{ task.status?.name || '—' }}
                        </p>
                        <p v-if="task.categories?.length" class="mt-1 text-[10px] text-indigo-600">
                            {{ task.categories.map(c => c.name).join(', ') }}
                        </p>
                    </div>
                    <button
                        @click="promptDelete(task.id)"
                        class="text-sm font-medium text-red-600 hover:text-red-800"
                    >
                        Delete
                    </button>
                </div>
                <p class="mt-1 text-[10px] text-gray-400">
                    Created {{ formatTs(task.created_at) }} • Updated {{ formatTs(task.updated_at) }}
                </p>
            </li>
            <li v-if="!tasksData.length" class="text-sm text-gray-500">No tasks yet.</li>
        </ul>

        <!-- pagination -->
        <div v-if="pagination.total > pagination.per_page" class="flex justify-center gap-2 pt-2 text-sm">
            <button @click="changePage(pagination.current_page - 1)" :disabled="!pagination.prev_page_url"
                    class="rounded border border-gray-300 px-3 py-1 hover:bg-gray-100 disabled:opacity-50">
                Prev
            </button>
            <span>Page {{ pagination.current_page }} / {{ pagination.last_page }}</span>
            <button @click="changePage(pagination.current_page + 1)" :disabled="!pagination.next_page_url"
                    class="rounded border border-gray-300 px-3 py-1 hover:bg-gray-100 disabled:opacity-50">
                Next
            </button>
        </div>

        <!-- confirm delete modal -->
        <div v-if="confirmDeleteId !== null" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="w-80 rounded bg-white p-6 shadow-lg">
                <p class="mb-4 text-gray-800">Delete this task?</p>
                <div class="flex justify-end gap-2">
                    <button @click="cancelDelete" :disabled="deleteProcessing"
                            class="rounded border border-gray-300 px-4 py-2 text-sm hover:bg-gray-100 disabled:opacity-50">
                        Cancel
                    </button>
                    <button @click="confirmDelete" :disabled="deleteProcessing"
                            class="flex items-center justify-center rounded bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50">
                        <svg v-if="deleteProcessing" class="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"/>
                            <path class="opacity-75" d="M12 2a10 10 0 00-3 19.5" stroke-width="4"
                                  stroke-linecap="round"/>
                        </svg>
                        {{ deleteProcessing ? 'Deleting…' : 'Delete' }}
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import {ref, computed, watch} from 'vue';
import {useForm, router} from '@inertiajs/vue3';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';
import {useTaskFilterStore} from "@/stores/taskFilters.js";

const props = defineProps({
    tasks: {type: Object, required: true},
    priorities: {type: Array, required: true},
    statuses: {type: Array, required: true},
    categories: {type: Array, required: true},
});

const form = useForm({
    title: '',
    priority_id: '',
    status_id: '',
    due_date: '',
    categories: [],
});

const categories = computed(() => props.categories);
const statuses = computed(() => props.statuses);
const filter = useTaskFilterStore();

const selectedCategoryObjs = computed({
    get: () => categories.value.filter(c => filter.categoryIds.includes(c.id)),
    set: v => (filter.categoryIds = v.map(c => c.id)),
});

const selectedStatusObjs = computed({
    get: () => statuses.value.filter(s => filter.statusIds.includes(s.id)),
    set: v => (filter.statusIds = v.map(s => s.id)),
});

watch(() => filter.params, p => {
    router.get(route('dashboard'), p, {preserveScroll: true, replace: true});
}, {deep: true});

function submit() {
    form.transform(data => ({
        ...data,
        category_ids: data.categories.map(c => c.id),
    })).post(route('tasks.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('title', 'priority_id', 'due_date', 'categories'),
    });
}

/* pagination helpers */
const pagination = computed(() => props.tasks);
const tasksData = computed(() => props.tasks.data || []);

function changePage(page) {
    if (page === pagination.value.current_page) return;
    router.get(route('dashboard', {page}), {}, {preserveScroll: true});
}

/* delete */
const confirmDeleteId = ref(null);
const deleteProcessing = ref(false);

function promptDelete(id) {
    confirmDeleteId.value = id;
}

function cancelDelete() {
    if (!deleteProcessing.value) confirmDeleteId.value = null;
}

function confirmDelete() {
    deleteProcessing.value = true;
    router.delete(route('tasks.destroy', confirmDeleteId.value), {
        preserveScroll: true,
        onSuccess: () => {
            deleteProcessing.value = false;
            confirmDeleteId.value = null;
        },
        onFinish: () => {
            deleteProcessing.value = false;
        },
    });
}

function formatTs(ts) {
    return new Date(ts).toLocaleString();
}

const priorities = computed(() => props.priorities);
</script>
