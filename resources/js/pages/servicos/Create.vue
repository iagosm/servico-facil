<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import { Button } from '@/components/ui/button'
import ServicoForm from '@/components/servicos/ServicoForm.vue'
import type { Tecnico, EstoqueItem } from '@/components/servicos/ServicoForm.vue'
import type { ClienteSimples } from '@/components/clientes/ClienteQuickCreate.vue'

type Props = {
    clientes: ClienteSimples[]
    tecnicos: Tecnico[]
    supervisores: { id: number; name: string }[]
    estoque: EstoqueItem[]
}

defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Serviços', href: '/servicos' },
    { title: 'Nova OS', href: '/servicos/create' },
]
</script>

<template>
    <Head title="Nova OS" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 max-w-4xl">
            <ServicoForm
                :clientes="clientes"
                :tecnicos="tecnicos"
                :supervisores="supervisores"
                mode="create"
                @cancel="$router.visit('/servicos')"
            />
        </div>
    </AppLayout>
</template>