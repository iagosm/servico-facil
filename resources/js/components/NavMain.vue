<script setup lang="ts">
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';

const props = defineProps<{ items: NavItem[] }>();

const page = usePage();

const openGroups = ref<Record<string, boolean>>(
    Object.fromEntries(
        props.items
            .filter(item => item.children)
            .map(item => [
                item.title,
                item.children!.some(child => page.url.startsWith(child.href ?? '')),
            ])
    )
);
</script>

<template>
    <SidebarGroup>
        <SidebarGroupLabel>Menu</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">

                <!-- Grupo com filhos → collapsible -->
                <Collapsible
                    v-if="item.children"
                    v-model:open="openGroups[item.title]"
                    as-child
                >
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :tooltip="item.title">
                                <component :is="item.icon" v-if="item.icon" />
                                <span>{{ item.title }}</span>
                                <ChevronRight
                                    class="ml-auto transition-transform duration-200"
                                    :class="{ 'rotate-90': openGroups[item.title] }"
                                />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>

                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem
                                    v-for="child in item.children"
                                    :key="child.title"
                                >
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="page.url.startsWith(child.href ?? '')"
                                    >
                                        <Link :href="child.href ?? '#'">
                                            <component :is="child.icon" v-if="child.icon" />
                                            <span>{{ child.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <!-- Item simples → link direto -->
                <SidebarMenuItem v-else>
                  <SidebarMenuButton
                      as-child
                      :tooltip="item.title"
                      :is-active="page.url === item.href || page.url.startsWith(item.href + '/')"
                  >
                      <Link :href="item.href ?? '#'">
                          <component :is="item.icon" v-if="item.icon" />
                          <span>{{ item.title }}</span>
                      </Link>
                  </SidebarMenuButton>
              </SidebarMenuItem>

            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>