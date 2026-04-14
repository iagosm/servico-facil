<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'

type Column = {
    key: string;
    header: string;
    class?: string;
    sortable?: boolean;
};

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Pagination = {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
};

type Sort = {
    column: string;
    direction: 'asc' | 'desc';
};

type Props = {
    columns: Column[];
    rows: Array<Record<string, unknown>>;
    rowKey?: string;
    emptyMessage?: string;
    pagination?: Pagination;
    sort?: Sort;
    routeName?: string;
    search?: string;
    searchPlaceholder?: string;
    perPage?: number;
    perPageOptions?: number[];
    loading?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    rowKey: 'id',
    emptyMessage: 'Nenhum registro encontrado.',
    searchPlaceholder: 'Buscar...',
    perPage: 10,
    perPageOptions: () => [10, 20, 30, 50],
    loading: false,
})

const emit = defineEmits<{
    'update:search': [value: string]
    'update:perPage': [value: number]
}>()

const paginacaoTexto = computed(() => {
    if (!props.pagination) return ''

    const { current_page, per_page, total } = props.pagination
    const de = (current_page - 1) * per_page + 1
    const ate = Math.min(current_page * per_page, total)

    return `${de}–${ate} de ${total} registro${total !== 1 ? 's' : ''}`
})

const linksVisiveis = computed(() => {
    if (!props.pagination) return []

    return props.pagination.links
})

function ordenarPor(column: Column) {
    if (!column.sortable) return

    const direction =
        props.sort?.column === column.key && props.sort.direction === 'asc'
            ? 'desc'
            : 'asc'

    router.get(
        window.location.pathname,
        {
            ...Object.fromEntries(new URLSearchParams(window.location.search)),
            sort: column.key,
            direction,
        },
        { preserveState: true, replace: true },
    )
}

function iconeOrdenacao(column: Column) {
    if (!column.sortable) return null

    if (props.sort?.column !== column.key) return 'neutro'

    return props.sort.direction === 'asc' ? 'asc' : 'desc'
}
</script>

<template>
    <div class="space-y-3">

        <!-- Barra superior: perPage + Busca -->
        <div class="flex items-center justify-end gap-2">
            <select
                :value="perPage"
                @change="emit('update:perPage', Number(($event.target as HTMLSelectElement).value))"
                class="h-9 rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring cursor-pointer"
            >
                <option v-for="opt in perPageOptions" :key="opt" :value="opt">
                    {{ opt }} por página
                </option>
            </select>

            <input
                :value="search"
                @input="emit('update:search', ($event.target as HTMLInputElement).value)"
                :placeholder="searchPlaceholder"
                class="max-w-sm h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
            />
        </div>

        <!-- Tabela com loading overlay -->
        <div class="relative overflow-hidden rounded-xl border">

            <!-- Overlay de loading -->
            <Transition name="fade">
                <div
                    v-if="loading"
                    class="absolute inset-0 z-10 flex items-center justify-center bg-background/60 backdrop-blur-[2px]"
                >
                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                        <svg
                            class="h-4 w-4 animate-spin"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        Carregando...
                    </div>
                </div>
            </Transition>

            <table class="w-full text-sm">
                <thead class="bg-muted/40 text-left">
                    <tr>
                        <th
                            v-for="column in columns"
                            :key="column.key"
                            class="px-4 py-3 font-medium text-muted-foreground"
                            :class="[
                                column.class,
                                column.sortable ? 'cursor-pointer select-none hover:text-foreground transition-colors' : '',
                            ]"
                            @click="ordenarPor(column)"
                        >
                            <div class="flex items-center gap-1">
                                {{ column.header }}
                                <span v-if="column.sortable" class="flex flex-col leading-none">
                                    <svg
                                        class="w-3 h-3 transition-colors"
                                        :class="iconeOrdenacao(column) === 'asc' ? 'text-foreground' : 'text-muted-foreground/40'"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
                                    </svg>
                                    <svg
                                        class="w-3 h-3 -mt-1 transition-colors"
                                        :class="iconeOrdenacao(column) === 'desc' ? 'text-foreground' : 'text-muted-foreground/40'"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="rows.length === 0">
                        <td :colspan="columns.length">
                            <div class="flex justify-center items-center py-10 text-muted-foreground">
                                {{ emptyMessage }}
                            </div>
                        </td>
                    </tr>

                    <tr
                        v-for="row in rows"
                        v-else
                        :key="(row as any)[rowKey]"
                        class="border-t transition-colors hover:bg-muted/20"
                    >
                        <td
                            v-for="column in columns"
                            :key="column.key"
                            class="px-4 py-3"
                            :class="column.class"
                        >
                            <slot
                                :name="`cell-${column.key}`"
                                :row="row"
                                :value="(row as any)[column.key]"
                            >
                                {{ (row as any)[column.key] ?? '-' }}
                            </slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Rodapé: texto + paginação -->
        <div v-if="pagination" class="flex items-center justify-between text-sm px-1">
            <span class="text-muted-foreground">
                {{ paginacaoTexto }}
            </span>

            <div class="flex items-center gap-1">
                <template v-for="link in linksVisiveis" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        preserve-state
                        class="min-w-[32px] h-8 px-2 rounded border text-sm transition-colors flex items-center justify-center"
                        :class="link.active
                            ? 'bg-primary border-primary text-primary-foreground font-medium'
                            : 'border-border text-muted-foreground hover:text-foreground hover:border-border'"
                        v-html="link.label"
                    />
                    <span
                        v-else
                        class="min-w-[32px] h-8 px-2 rounded border border-border text-muted-foreground opacity-40 cursor-not-allowed text-sm flex items-center justify-center"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>

    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>