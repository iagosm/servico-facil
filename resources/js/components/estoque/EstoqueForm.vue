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
    descricao: string;
    sku: string;
    quantidade: string;
    quantidade_minima: string;
    preco_custo: string;
    preco_venda: string;
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
    ? '/estoque'
    : `/estoque/${props.cliente?.id}?_method=PATCH`;

const submitLabel = props.mode === 'create' ? 'Salvar' : 'Atualizar';
const title = props.mode === 'create' ? 'Novo Item' : 'Editar Item';
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
                        placeholder="Nome Item"
                        :default-value="cliente?.nome ?? ''"
                    />
                    <InputError :message="errors.nome" />
                </div>
                <div class="grid gap-2">
                    <Label for="descricao">Descrição</Label>
                    <Input
                        id="descricao"
                        name="descricao"
                        type="text"
                        required
                        placeholder="Descrição"
                        :default-value="cliente?.descricao ?? ''"
                    />
                    <InputError :message="errors.nome" />
                </div>
                <div class="grid gap-2">
                    <Label for="sku">SKU</Label>
                    <Input
                        id="sku"
                        name="sku"
                        type="text"
                        required
                        placeholder="SKU"
                        :default-value="cliente?.sku ?? ''"
                    />
                    <InputError :message="errors.sku" />
                </div>
                <div class="grid gap-2">
                    <Label for="quantidade">Quantidade</Label>
                    <Input
                        id="quantidade"
                        name="quantidade"
                        type="text"
                        required
                        placeholder="Quantidade"
                        :default-value="cliente?.quantidade ?? ''"
                    />
                    <InputError :message="errors.sku" />
                </div>
                <div class="grid gap-2">
                    <Label for="quantidade_minima">Quantidade Mínima</Label>
                    <Input
                        id="quantidade_minima"
                        name="quantidade_minima"
                        type="text"
                        required
                        placeholder="Quantidade Mínima"
                        :default-value="cliente?.quantidade_minima ?? ''"
                    />
                    <InputError :message="errors.quantidade_minima" />
                </div>
                <div class="grid gap-2">
                    <Label for="preco_custo">Preço Custo</Label>
                    <Input
                        id="preco_custo"
                        name="preco_custo"
                        type="text"
                        required
                        placeholder="Preço Custo"
                        :default-value="cliente?.preco_custo ?? ''"
                    />
                    <InputError :message="errors.telefone" />
                </div>

                <div class="grid gap-2">
                    <Label for="preco_venda">Preço Venda</Label>
                    <Input
                        id="preco_venda"
                        name="preco_venda"
                        type="text"
                        placeholder="Preço Venda"
                        :default-value="cliente?.preco_venda ?? ''"
                    />
                    <InputError :message="errors.email" />
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