<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

export type Equipe = {
    id?: number;
    name: string;
    email: string;
    cargo: string;
    telefone: string;
    supervisor_id: string;
    senha: string;
    ativo: string;
};

export type Supervisor = {
  id: number
  name: string
}

type Props = {
    mode: 'create' | 'edit';
    equipe?: Equipe | null;
    supervisores?: { id: number; name: string }[];
};

const props = defineProps<Props>();
const emit = defineEmits<{
    cancel: [];
}>();

console.log('Props recebidas:', props);
const actionUrl = props.mode === 'create'
    ? '/equipe'
    : `/equipe/${props.equipe?.id}?_method=PATCH`;

const submitLabel = props.mode === 'create' ? 'Salvar' : 'Atualizar';
const title = props.mode === 'create' ? 'Novo Membro' : 'Editar Membro';
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
            <!-- Nome -->
            <div class="grid gap-2">
                <Label for="name">Nome</Label>
                <Input
                    id="name"
                    name="name"
                    type="text"
                    required
                    placeholder="Nome do colaborador"
                    :default-value="equipe?.name ?? ''"
                />
                <InputError :message="errors.name" />
            </div>

            <!-- Email -->
            <div class="grid gap-2">
                <Label for="email">E-mail</Label>
                <Input
                    id="email"
                    name="email"
                    type="email"
                    required
                    placeholder="usuario@email.com"
                    :default-value="equipe?.email ?? ''"
                />
                <InputError :message="errors.email" />
            </div>

            <!-- Cargo -->
            <div class="grid gap-2">
                <Label for="cargo">Cargo</Label>
                <Input
                    id="cargo"
                    name="cargo"
                    type="text"
                    required
                    placeholder="Cargo do colaborador"
                    :default-value="equipe?.cargo ?? ''"
                />
                <InputError :message="errors.cargo" />
            </div>

            <!-- Telefone -->
            <div class="grid gap-2">
                <Label for="telefone">Telefone</Label>
                <Input
                    id="telefone"
                    name="telefone"
                    type="text"
                    required
                    placeholder="(XX) XXXX-XXXX"
                    :default-value="equipe?.telefone ?? ''"
                />
                <InputError :message="errors.telefone" />
            </div>

            <!-- Supervisor -->
            <div class="grid gap-2">
                <Label for="supervisor_id">Supervisor</Label>
                <select
                    id="supervisor_id"
                    name="supervisor_id"
                    class="border rounded px-2 py-1"
                    :value="equipe?.supervisor_id ?? ''"
                    required
                >
                    <option value="">Selecione</option>
                    <option
                        v-for="sup in props.supervisores ?? []"
                        :key="sup.id"
                        :value="sup.id"
                    >
                        {{ sup.name }}
                    </option>
                </select>
                <InputError :message="errors.supervisor_id" />
            </div>

            <!-- Senha -->
            <div class="grid gap-2">
                <Label for="password">Senha</Label>
                <Input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Senha de acesso"
                />
                <InputError :message="errors.senha" />
            </div>

            <!-- Ativo -->
            <div class="grid gap-2">
                <Label for="ativo">Ativo</Label>
                <select
                    id="ativo"
                    name="ativo"
                    class="border rounded px-2 py-1"
                    :value="equipe?.ativo == 'Sim' ? 'S' : 'N'"
                    required
                >
                    <option value="">Selecione</option>
                    <option value="S">Sim</option>
                    <option value="N">Não</option>
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