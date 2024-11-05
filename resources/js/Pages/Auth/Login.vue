<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Link, useForm } from '@inertiajs/vue3'

import { ref } from 'vue'

const form = useForm({
    email: '',
    password: '',
});

const errors = ref({});

const submit = () => {
    form.post('/login', {
        onError:(error) => {
            errors.value = error;
        }
    });
}
</script>

<template>
    <div class=" mx-auto h-screen w-screen flex justify-center items-center ">
        <Card class="mx-auto max-w-sm h-100">
            <CardHeader>
                <CardTitle class="text-2xl">
                    Login
                </CardTitle>
                <CardDescription>
                    Enter your email below to login to your account
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit">
                    <div class="grid gap-4">
                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                v-model="form.email"
                                :class="{'is-invalid': errors.email}"
                                placeholder="m@example.com"
                                required
                            />
                            <span v-if="errors.email">{{ errors.email }}</span>
                        </div>
                        <div class="grid gap-2">
                            <div class="flex items-center">
                                <Label for="password">Password</Label>
                                <a href="#" class="ml-auto inline-block text-sm underline">
                                Forgot your password?
                                </a>
                            </div>
                            <Input 
                                id="password" 
                                type="password" 
                                v-model="form.password"
                                :class="{'is-invalid': errors.password}"
                                required 
                            />
                            <span v-if="errors.password">{{ errors.password }}</span>
                        </div>
                        <Button type="submit" class="w-full">
                        Login
                        </Button>
                        <!-- <Button variant="outline" class="w-full">
                        Login with Google
                        </Button> -->
                    </div>
                </form>
                <div class="mt-4 text-center text-sm">
                    Don't have an account?
                    <Link href="/signup" class="underline">
                        Register
                    </Link>
                </div>
            </CardContent>
        </Card>
    </div>
</template>