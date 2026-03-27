<script setup lang="ts">
type Column = {
    key: string;
    header: string;
    class?: string;
};

type Props = {
    columns: Column[];
    rows: Array<Record<string, unknown>>;
    rowKey?: string;
    emptyMessage?: string;
};

const props = withDefaults(defineProps<Props>(), {
    rowKey: 'id',
    emptyMessage: 'Nenhum registro encontrado.',
});
</script>

<template>
    <div class="overflow-hidden rounded-xl border">
        <table class="w-full text-sm">
            <thead class="bg-muted/40 text-left">
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column.key"
                        class="px-4 py-3"
                        :class="column.class"
                    >
                        {{ column.header }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="rows.length === 0">
                    <td :colspan="columns.length">
                        <div class="flex justify-center items-center py-6 text-muted-foreground">
                            {{ emptyMessage }}
                        </div>
                    </td>
                </tr>

                <tr
                    v-for="row in rows"
                    v-else
                    :key="(row as any)[rowKey]"
                    class="border-t"
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
                            {{
                                (row as any)[column.key] ?? '-'
                            }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

