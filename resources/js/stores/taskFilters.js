import {defineStore} from 'pinia';
import {useStorage} from '@vueuse/core';

export const useTaskFilterStore = defineStore('taskFilters', {
    state: () => ({
        categoryIds: useStorage('task.categoryIds', []),   // array<int>
        statusIds: useStorage('task.statusIds', []),
        from: useStorage('task.from', ''),
        to: useStorage('task.to', ''),
    }),
    getters: {
        hasFilters: s =>
            !!(s.categoryIds.length || s.statusIds.length || s.from || s.to),

        params: s => ({
            category_ids: s.categoryIds.join(','),
            status_ids: s.statusIds.join(','),
            from: s.from,
            to: s.to,
        }),
    },
});
