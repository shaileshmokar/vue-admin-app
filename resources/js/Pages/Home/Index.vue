<script setup>
import { Button } from '@/components/ui/button'
import Layout from '../../Layout/App.vue';
</script>

<template>
    <Layout>
        <div class="flex flex-col items-center gap-1 text-center">
            <h3 class="text-2xl font-bold tracking-tight">
                You have no products
            </h3>
            <p class="text-sm text-muted-foreground">
                You can start selling as soon as you add a product.
            </p>
            <Button class="mt-4">
                Add Product
            </Button>
        </div> 
    </Layout>
</template>