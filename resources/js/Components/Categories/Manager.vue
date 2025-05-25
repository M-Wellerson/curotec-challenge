<template>
    <div>
        <!-- open modal button -->
        <button @click="show = true" class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
            Manage categories
        </button>

        <!-- modal -->
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="w-96 rounded bg-white p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Categories</h3>

                <!-- create form -->
                <form @submit.prevent="create" class="flex gap-2">
                    <input v-model="form.name" type="text" placeholder="New category" class="flex-1 rounded border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-indigo-300" required />
                    <button type="submit" :disabled="form.processing || !form.name.trim()" class="flex items-center justify-center rounded bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50">
                        <svg v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4" /><path class="opacity-75" d="M12 2a10 10 0 00-3 19.5" stroke-width="4" stroke-linecap="round" /></svg>
                        Add
                    </button>
                </form>

                <!-- list -->
                <ul class="my-4 max-h-40 overflow-y-auto pr-1 text-sm">
                    <li v-for="c in categories" :key="c.id" class="border-b py-1 last:border-b-0">
                        {{ c.name }}
                    </li>
                    <li v-if="!categories.length" class="text-gray-500">No categories.</li>
                </ul>

                <div class="mt-4 text-right">
                    <button @click="close" :disabled="form.processing" class="rounded border border-gray-300 px-4 py-1 text-sm hover:bg-gray-100">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    categories: { type: Array, required: true },
});

const show = ref(false);
function close() { show.value = false; }

const form = useForm({ name: '' });
function create() {
    form.post(route('categories.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('name'),
    });
}

const categories = computed(() => props.categories);
</script>
