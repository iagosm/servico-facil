<script setup lang="ts">
import { Form, Head, router } from '@inertiajs/vue3'
import { useDebounce } from '@vueuse/core'
import { computed, ref, watch } from 'vue'
import EquipeForm from '@/components/equipe/EquipeForm.vue'
import GenericTable from '@/components/tables/GenericTable.vue'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { Spinner } from '@/components/ui/spinner'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'

type Equipe = {
    id: number;
    name: string;
    email: string;
    cargo: string;
    telefone: string;
    ativo: boolean;
    supervisor?: { name: string };
    supervisor_id?: number;
};

type Props = {
    equipe: {
        data: Equipe[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    supervisores: Array<{ id: number; name: string }>;
    filters: { search?: string; perPage?: number };
};

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Equipe', href: '/equipe' }]

// ── Busca com debounce ──────────────────────────────────
const search = ref(props.filters.search ?? '')
const searchDebounced = useDebounce(search, 500)

// ── Quantidade por página ───────────────────────────────
const perPage = ref(props.filters.perPage ?? 10)

// ── Loading ─────────────────────────────────────────────
const loading = ref(false)

router.on('start', () => {
    loading.value = true
})

router.on('finish', () => {
    loading.value = false
})

// ── Navegação com filtros ───────────────────────────────
function navegarComFiltros() {
    const params: Record<string, string | number> = {
        perPage: perPage.value,
    }

    if (searchDebounced.value) {
        params.search = searchDebounced.value
    }

    router.get('/equipe', params, {
        preserveState: true,
        replace: true,
    })
}

watch(searchDebounced, navegarComFiltros)
watch(perPage, navegarComFiltros)

// ── Tabela ─────────────────────────────────────────────
const columns = [
    { key: 'name',       header: 'Nome' },
    { key: 'email',      header: 'E-mail' },
    { key: 'cargo',      header: 'Cargo' },
    { key: 'telefone',   header: 'Telefone' },
    { key: 'supervisor', header: 'Supervisor' },
    { key: 'ativo',      header: 'Ativo' },
    { key: 'actions',    header: 'Ações' },
]

const rows = computed(() =>
    props.equipe.data.map(e => ({
        ...e,
        supervisor_name: e.supervisor?.name ?? '',
        telefone: e.telefone ?? '',
        ativo: e.ativo ? 'Sim' : 'Não',
    })),
)

// ── Painel ─────────────────────────────────────────────
const activePane = ref<'list' | 'create' | 'edit'>('list')
const selectedEquipe = ref<Equipe | null>(null)

const openCreate = () => {
    selectedEquipe.value = null
    activePane.value = 'create'
}

const openEdit = (equipe: Equipe) => {
    selectedEquipe.value = equipe
    activePane.value = 'edit'
}

const backToList = () => {
    activePane.value = 'list'
}
</script>

<template>
    <Head title="Equipe" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-semibold">Equipe</h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Gerencie os membros da equipe e seus acessos
                    </p>
                </div>

                <div class="flex gap-2">
                    <Button
                        type="button"
                        :variant="activePane === 'list' ? 'default' : 'secondary'"
                        size="sm"
                        @click="activePane = 'list'"
                    >
                        Lista
                    </Button>
                    <Button
                        type="button"
                        :variant="activePane === 'create' ? 'default' : 'secondary'"
                        size="sm"
                        @click="openCreate()"
                    >
                        Adicionar
                    </Button>
                </div>
            </div>

            <div class="mt-6 space-y-4">

                <!-- Lista -->
                <div v-if="activePane === 'list'">
                    <GenericTable
                        :columns="columns"
                        :rows="rows"
                        :pagination="equipe"
                        :loading="loading"
                        v-model:search="search"
                        v-model:per-page="perPage"
                        search-placeholder="Buscar por nome, e-mail ou cargo..."
                    >
                        <template #cell-supervisor="{ row }">
                            {{ row.supervisor_name }}
                        </template>

                        <template #cell-actions="{ row }">
                            <div class="flex items-center gap-2">
                                <Button type="button" size="sm" @click="openEdit(row as Equipe)">
                                    Editar
                                </Button>

                                <Dialog>
                                    <DialogTrigger as-child>
                                        <Button type="button" variant="destructive" size="sm">Excluir</Button>
                                    </DialogTrigger>
                                    <DialogContent>
                                        <DialogHeader class="space-y-2">
                                            <DialogTitle>Excluir membro?</DialogTitle>
                                            <DialogDescription>Esta ação não pode ser desfeita.</DialogDescription>
                                        </DialogHeader>
                                        <Form
                                            :action="`/equipe/${row.id}?_method=DELETE`"
                                            method="post"
                                            class="space-y-6"
                                            @success="backToList"
                                            v-slot="{ processing }"
                                        >
                                            <DialogFooter class="gap-2">
                                                <DialogClose as-child>
                                                    <Button type="button" variant="secondary" :disabled="processing">
                                                        Cancelar
                                                    </Button>
                                                </DialogClose>
                                                <Button type="submit" variant="destructive" :disabled="processing">
                                                    <Spinner v-if="processing" class="mr-2 inline" />
                                                    Confirmar
                                                </Button>
                                            </DialogFooter>
                                        </Form>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </template>
                    </GenericTable>
                </div>

                <div v-else-if="activePane === 'create'">
                    <EquipeForm
                        mode="create"
                        :supervisores="props.supervisores"
                        key="create-pane"
                        @cancel="backToList"
                    />
                </div>

                <div v-else-if="activePane === 'edit'">
                    <EquipeForm
                        mode="edit"
                        :equipe="selectedEquipe"
                        :supervisores="props.supervisores"
                        :key="selectedEquipe?.id ?? 'edit-null'"
                        @cancel="backToList"
                    />
                </div>

            </div>
        </div>
    </AppLayout>
</template>