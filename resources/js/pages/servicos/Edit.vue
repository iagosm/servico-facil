<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import type { BreadcrumbItem } from '@/types'
import ServicoForm from "@/components/servicos/ServicoForm.vue"
import type { Tecnico, EstoqueItem } from '@/components/servicos/ServicoForm.vue'

type Equipamento = {
    id?: number
    tipo: string
    marca?: string
    modelo?: string
    numero_serie?: string
    condicao_entrada?: string
    problemas: { id?: number; descricao: string; resolvido?: boolean }[]
}

type Servico = {
    id: number
    numero: string
    cliente_id: number
    supervisor_id?: number | null
    tipo: 'diagnostico' | 'reparo' | 'orcamento'
    prioridade: 'normal' | 'urgente' | 'aguardando_aprovacao'
    obs_internas?: string
    obs_cliente?: string
    valor_cobrado?: number
    data_previsao?: string
    validade_orcamento?: string
    equipamentos: Equipamento[]
    itens_cliente: { id?: number; descricao: string }[]
    users: { id: number; name: string; cargo: string; pivot: { papel: string } }[]
}

type Props = {
    servico: Servico
    clientes: ClienteSimples[]
    tecnicos: Tecnico[]
    estoque: EstoqueItem[]
    supervisores: { id: number; name: string }[]
}

const props = defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Serviços', href: '/servicos' },
    { title: props.servico.numero, href: `/servicos/${props.servico.id}` },
    { title: 'Editar', href: '#' },
]
</script>

<template>
    <Head :title="`Editar OS ${servico.numero}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <ServicoForm
                mode="edit"
                :servico="servico"
                :clientes="clientes"
                :tecnicos="tecnicos"
                :estoque="estoque"
                :supervisores="supervisores"
                @cancel="$inertia.visit(`/servicos/${servico.id}`)"
            />
        </div>
    </AppLayout>
</template>