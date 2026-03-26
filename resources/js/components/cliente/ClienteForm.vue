<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

export type Cliente = {
    id?: number;
    nome: string;
    telefone: string;
    email?: string | null;
    documento?: string | null;
    endereco?: string | null;
    cidade?: string | null;
    estado?: string | null;
    cep?: string | null;
    observacoes?: string | null;
};

type Props = {
    mode: 'create' | 'edit';
    cliente?: Cliente | null;
};

const props = defineProps<Props>();
const emit = defineEmits<{
    cancel: [];
}>();

const actionUrl = props.mode === 'create'
    ? '/clientes'
    : `/clientes/${props.cliente?.id}?_method=PATCH`;

const submitLabel = props.mode === 'create' ? 'Salvar' : 'Atualizar';
const title = props.mode === 'create' ? 'Novo Cliente' : 'Editar Cliente';
</script>

<template>
    <div class="space-y-4">
        <Heading :variant="'small'" :title="title" />

        <Form
            :action="actionUrl"
            method="post"
            class="space-y-6"
            @success="emit('cancel')"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-6 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="nome">Nome</Label>
                    <Input
                        id="nome"
                        name="nome"
                        type="text"
                        required
                        placeholder="Nome do cliente"
                        :default-value="cliente?.nome ?? ''"
                    />
                    <InputError :message="errors.nome" />
                </div>

                <div class="grid gap-2">
                    <Label for="telefone">Telefone</Label>
                    <Input
                        id="telefone"
                        name="telefone"
                        type="text"
                        required
                        placeholder="Telefone"
                        :default-value="cliente?.telefone ?? ''"
                    />
                    <InputError :message="errors.telefone" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        placeholder="email@exemplo.com"
                        :default-value="cliente?.email ?? ''"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="documento">Documento</Label>
                    <Input
                        id="documento"
                        name="documento"
                        type="text"
                        placeholder="Documento (opcional)"
                        :default-value="cliente?.documento ?? ''"
                    />
                    <InputError :message="errors.documento" />
                </div>

                <div class="grid gap-2 md:col-span-2">
                    <Label for="endereco">Endereço</Label>
                    <Input
                        id="endereco"
                        name="endereco"
                        type="text"
                        placeholder="Endereço (opcional)"
                        :default-value="cliente?.endereco ?? ''"
                    />
                    <InputError :message="errors.endereco" />
                </div>

                <div class="grid gap-2">
                    <Label for="cidade">Cidade</Label>
                    <Input
                        id="cidade"
                        name="cidade"
                        type="text"
                        placeholder="Cidade (opcional)"
                        :default-value="cliente?.cidade ?? ''"
                    />
                    <InputError :message="errors.cidade" />
                </div>

                <div class="grid gap-2">
                    <Label for="estado">Estado</Label>
                    <Input
                        id="estado"
                        name="estado"
                        type="text"
                        placeholder="Estado (opcional)"
                        :default-value="cliente?.estado ?? ''"
                    />
                    <InputError :message="errors.estado" />
                </div>

                <div class="grid gap-2">
                    <Label for="cep">CEP</Label>
                    <Input
                        id="cep"
                        name="cep"
                        type="text"
                        placeholder="CEP (opcional)"
                        :default-value="cliente?.cep ?? ''"
                    />
                    <InputError :message="errors.cep" />
                </div>

                <div class="grid gap-2 md:col-span-2">
                    <Label for="observacoes">Observações</Label>
                    <textarea
                        id="observacoes"
                        name="observacoes"
                        rows="4"
                        class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]"
                        placeholder="Observações (opcional)"
                    >{{ cliente?.observacoes ?? '' }}</textarea>
                    <InputError :message="errors.observacoes" />
                </div>
            </div>

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="processing">
                    <Spinner v-if="processing" class="mr-2 inline" />
                    {{ submitLabel }}
                </Button>

                <Button
                    type="button"
                    variant="secondary"
                    :disabled="processing"
                    @click="emit('cancel')"
                >
                    Cancelar
                </Button>
            </div>
        </Form>
    </div>
</template>