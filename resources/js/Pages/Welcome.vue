<template>

    <Head title="Welcome" />
    <v-app>
        <v-container fluid>
            <v-row>
                <v-col :cols="8">
                    <v-icon color="primary">
                        mdi-laravel
                    </v-icon>
                </v-col>
                <v-col :cols="4">
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')">
                    <v-btn color="primary" class="mr-4">Dashboard</v-btn>
                    </Link>
                    <Link v-if="canLogin && !$page.props.auth.user" :href="route('login')">
                    <v-btn color="primary" class="mr-4">Log in</v-btn>
                    </Link>
                    <Link v-if="canRegister && !$page.props.auth.user" :href="route('register')">
                    <v-btn color="secondary">Register</v-btn>
                    </Link>
                </v-col>
            </v-row>
        </v-container>
    </v-app>
</template>

<script setup>
    defineProps({
        canLogin: {
            type: Boolean,
        },
        canRegister: {
            type: Boolean,
        },
    });
</script>
