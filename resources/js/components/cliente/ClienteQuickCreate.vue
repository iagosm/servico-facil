<script setup lang="ts">
import { ref, reactive } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import {
    Dialog, DialogTrigger, DialogContent, DialogHeader,
    DialogTitle, DialogDescription, DialogFooter, DialogClose,
} from '@/components/ui/dialog'
import InputError from '@/components/InputError.vue'

export type ClienteSimples = {
    id: number
    nome: string
    telefone: string
    email?: string | null
    documento?: string | null
}

const emit = defineEmits<{
    criado: [cliente: ClienteSimples]
}>()

const open    = ref(false)
const loading = ref(false)
const errors  = reactive<Record<string, string>>({})

const form = reactive({ nome: '', telefone: '', email: '', documento: '' })

function resetForm() {
    form.nome = ''; form.telefone = ''; form.email = ''; form.documento = ''
    Object.keys(errors).forEach(k => delete errors[k])
}

async function salvar() {
    Object.keys(errors).forEach(k => delete errors[k])
    if (!form.nome.trim())     { errors.nome = 'Nome é obrigatório.'; return }
    if (!form.telefone.trim()) { errors.telefone = 'Telefone é obrigatório.'; return }

    loading.value = true
    try {
        const res = await fetch('/servicos/cliente-rapido', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '',
                'Accept': 'application/json',
            },
            body: JSON.stringify(form),
        })

        if (!res.ok) {
            const data = await res.json()
            if (data.errors) {
                Object.assign(errors, Object.fromEntries(
                    Object.entries(data.errors).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v])
                ))
            }
            return
        }

        const cliente: ClienteSimples = await res.json()
        emit('criado', cliente)
        open.value = false
        resetForm()
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <Button
                type="button"
                variant="outline"
                size="sm"
                class="whitespace-nowrap border-violet-500/60 text-violet-700 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-500/10"
                @click="resetForm"
            >
                + Novo cliente
            </Button>
        </DialogTrigger>

        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Cadastro rápido de cliente</DialogTitle>
                <DialogDescription>
                    Preencha os dados básicos. Você pode completar o cadastro depois em Clientes.
                </DialogDescription>
            </DialogHeader>

            <div class="grid gap-4 py-2">
                <div class="grid gap-2">
                    <Label for="qc-nome">Nome <span class="text-destructive">*</span></Label>
                    <Input
                        id="qc-nome"
                        v-model="form.nome"
                        placeholder="Nome completo ou empresa"
                        :class="errors.nome ? 'border-destructive' : ''"
                    />
                    <InputError :message="errors.nome" />
                </div>

                <div class="grid gap-2">
                    <Label for="qc-telefone">Telefone <span class="text-destructive">*</span></Label>
                    <Input
                        id="qc-telefone"
                        v-model="form.telefone"
                        placeholder="(00) 00000-0000"
                        :class="errors.telefone ? 'border-destructive' : ''"
                    />
                    <InputError :message="errors.telefone" />
                </div>

                <div class="grid gap-2">
                    <Label for="qc-email">Email</Label>
                    <Input
                        id="qc-email"
                        v-model="form.email"
                        type="email"
                        placeholder="email@exemplo.com (opcional)"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="qc-documento">CPF / CNPJ</Label>
                    <Input
                        id="qc-documento"
                        v-model="form.documento"
                        placeholder="Documento (opcional)"
                    />
                </div>
            </div>

            <DialogFooter class="gap-2">
                <DialogClose as-child>
                    <Button type="button" variant="secondary" :disabled="loading">Cancelar</Button>
                </DialogClose>
                <Button type="button" :disabled="loading" @click="salvar">
                    <Spinner v-if="loading" class="mr-2 inline" />
                    Salvar cliente
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>