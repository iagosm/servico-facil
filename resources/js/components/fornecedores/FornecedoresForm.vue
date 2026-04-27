<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import {vMaska} from 'maska/vue';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';


export type Fornecedores = {
    id?: number;
    nome: string;
    contato: string;
    telefone: string;
    email: string;
    site: string;
    observacoes: string;
    ativo: string;
};

type Props = {
    mode: 'create' | 'edit';
    fornecedores?: Fornecedores | null;
};

const props = defineProps<Props>();
const emit = defineEmits<{
    cancel: [];
}>();

const actionUrl = computed(() =>
    props.mode === 'create'
        ? '/fornecedores'
        : `/fornecedores/${props.fornecedores?.id}?_method=PATCH`
);

    console.log(props.fornecedores)
const submitLabel = props.mode === 'create' ? 'Salvar' : 'Atualizar';
const title = props.mode === 'create' ? 'Novo Fornecedor' : 'Editar Fornecedor';
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
                    <Label for="nome">Nome Fornecedor</Label>
                    <Input
                        id="nome"
                        name="nome"
                        type="text"
                        required
                        placeholder="Nome Fornecedor"
                        :default-value="fornecedores?.nome ?? ''"
                    />
                    <InputError :message="errors.nome" />
                </div>
                <div class="grid gap-2">
                    <Label for="contato">Contato</Label>
                    <Input
                        id="contato"
                        name="contato"
                        type="text"
                        required
                        placeholder="Contato"
                        :default-value="fornecedores?.contato ?? ''"
                    />
                    <InputError :message="errors.contato" /> <!-- ✅ era errors.nome -->
                </div>
                <div class="grid gap-2">
                    <Label for="telefone">Telefone</Label>
                    <Input
                        id="telefone"
                        name="telefone"
                        type="text"
                        required
                        v-maska="'(##) ####-####'"
                        placeholder="Telefone"
                        :default-value="fornecedores?.telefone ?? ''"
                    />
                    <InputError :message="errors.telefone" />
                </div>
                <div class="grid gap-2">
                    <Label for="email">E-mail</Label>
                    <Input
                        id="email"
                        name="email"
                        type="text"
                        required
                        placeholder="email@exemplo.com"
                        :default-value="fornecedores?.email ?? ''"
                    />
                    <InputError :message="errors.email" /> <!-- ✅ era errors.sku -->
                </div>
                <div class="grid gap-2">
                    <Label for="site">Site</Label>
                    <Input
                        id="site"
                        name="site"
                        type="text"
                        required
                        placeholder="Site"
                        :default-value="fornecedores?.site ?? ''"
                    />
                    <InputError :message="errors.site" />
                </div>
                <div class="grid gap-2">
                    <Label for="observacoes">Observações</Label>
                    <Input
                        id="observacoes"
                        name="observacoes"
                        type="text"
                        required
                        placeholder="Ex: Entrega em 3 dias úteis"
                        :default-value="fornecedores?.observacoes ?? ''"
                    />
                    <InputError :message="errors.observacoes" />
                </div>
                <div class="grid gap-2">
                    <Label for="ativo">Ativo</Label>
                    <select
                        name="ativo"
                        id="ativo"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    >
                        <!-- ✅ :selected para preservar valor ao editar -->
                        <option value="1" :selected="fornecedores?.ativo == '1'">Sim</option>
                        <option value="0" :selected="fornecedores?.ativo == '0'">Não</option>
                    </select>
                    <InputError :message="errors.ativo" />
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