<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { BreadcrumbItem } from '@/types';

type Cliente = {
    id: number;
    nome: string;
    telefone: string;
    email?: string | null;
    endereco?: string | null;
    cidade?: string | null;
    estado?: string | null;
    cep?: string | null;
    documento?: string | null;
    observacoes?: string | null;
};

type Props = {
    cliente: Cliente;
};

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Clientes', href: '/clientes' },
    { title: 'Editar', href: '#' },
];
</script>

<template>
    <Head title="Editar Cliente" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <Heading
                variant="small"
                title="Editar Cliente"
                description="Atualize os dados do cliente."
            />

            <Form
                :action="`/clientes/${cliente.id}?_method=PATCH`"
                method="post"
                class="mt-6 space-y-6"
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
                            :default-value="cliente.nome"
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
                            :default-value="cliente.telefone"
                        />
                        <InputError :message="errors.telefone" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input
                            id="email"
                            name="email"
                            type="email"
                            :default-value="cliente.email || ''"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="documento">Documento</Label>
                        <Input
                            id="documento"
                            name="documento"
                            type="text"
                            :default-value="cliente.documento || ''"
                        />
                        <InputError :message="errors.documento" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="endereco">Endereço</Label>
                        <Input
                            id="endereco"
                            name="endereco"
                            type="text"
                            :default-value="cliente.endereco || ''"
                        />
                        <InputError :message="errors.endereco" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="cidade">Cidade</Label>
                        <Input
                            id="cidade"
                            name="cidade"
                            type="text"
                            :default-value="cliente.cidade || ''"
                        />
                        <InputError :message="errors.cidade" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="estado">Estado</Label>
                        <Input
                            id="estado"
                            name="estado"
                            type="text"
                            :default-value="cliente.estado || ''"
                        />
                        <InputError :message="errors.estado" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="cep">CEP</Label>
                        <Input
                            id="cep"
                            name="cep"
                            type="text"
                            :default-value="cliente.cep || ''"
                        />
                        <InputError :message="errors.cep" />
                    </div>

                    <div class="grid gap-2 md:col-span-2">
                        <Label for="observacoes">Observações</Label>
                        <textarea
                            id="observacoes"
                            name="observacoes"
                            rows="4"
                            :value="cliente.observacoes || ''"
                            class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:ring-ring/50 focus-visible:ring-[3px]"
                        ></textarea>
                        <InputError :message="errors.observacoes" />
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Button type="submit" :disabled="processing">
                        Atualizar
                    </Button>
                    <Button
                        as-child
                        variant="secondary"
                        :disabled="processing"
                    >
                        <Link :href="`/clientes`">Cancelar</Link>
                    </Button>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>

