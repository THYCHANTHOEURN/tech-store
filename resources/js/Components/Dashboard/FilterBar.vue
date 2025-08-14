<template>
    <v-card class="mb-6 filter-card">
        <v-card-title>
            <v-icon class="mr-2">mdi-filter-variant</v-icon>
            {{ t('Filters') }}
            <v-spacer></v-spacer>
            <v-chip v-if="activeFiltersCount > 0" color="primary" size="small">
                {{ activeFiltersCount }} active {{ activeFiltersCount === 1 ? 'filter' : 'filters' }}
            </v-chip>
        </v-card-title>

        <v-card-text>
            <v-row>
                <slot name="filters"></slot>
            </v-row>

            <v-row v-if="showActiveFilters && hasActiveFilters" class="mt-2">
                <v-col cols="12">
                    <div class="d-flex flex-wrap">
                        <v-chip v-for="(filter, key) in activeFilterValues" :key="key" class="mr-2 mb-2" closable
                            @click:close="$emit('clear-filter', key)">
                            {{ filter.label }}: {{ filter.value }}
                        </v-chip>
                    </div>
                </v-col>
            </v-row>

            <v-divider class="my-2"></v-divider>

            <div class="d-flex justify-end align-center mt-2">
                <span v-if="totalItems !== null" class="text-caption text-medium-emphasis mr-4">
                    Total {{ totalItems }} {{ itemsLabel }}
                </span>
                <v-btn color="error" variant="outlined" size="small" :disabled="!hasActiveFilters || loading"
                    @click="$emit('reset-filters')" class="reset-btn">
                    <v-icon start>mdi-refresh</v-icon>
                    {{ t('Reset Filters') }}
                </v-btn>
            </div>
        </v-card-text>

        <v-overlay v-if="loading" absolute :opacity="0.1" class="d-flex align-center justify-center" contained>
            <v-progress-circular indeterminate color="primary"></v-progress-circular>
        </v-overlay>
    </v-card>
</template>

<script setup>
    import { computed } from 'vue';
    import { useI18n } from 'vue-i18n';

    const { t, locale } = useI18n();

    const props = defineProps({
        loading: {
            type: Boolean,
            default: false
        },
        totalItems: {
            type: Number,
            default: null
        },
        itemsLabel: {
            type: String,
            default: 'items'
        },
        activeFilters: {
            type: Object,
            default: () => ({})
        },
        showActiveFilters: {
            type: Boolean,
            default: true
        }
    });

    const emit = defineEmits(['reset-filters', 'clear-filter']);

    const hasActiveFilters = computed(() => {
        return Object.keys(props.activeFilters).some(key => {
            const filter = props.activeFilters[key];
            return filter.active === true;
        });
    });

    const activeFiltersCount = computed(() => {
        return Object.values(props.activeFilters).filter(f => f.active).length;
    });

    const activeFilterValues = computed(() => {
        const result = {};

        Object.entries(props.activeFilters).forEach(([key, filter]) => {
            if (filter.active) {
                result[key] = {
                    label: filter.label,
                    value: filter.displayValue || filter.value
                };
            }
        });

        return result;
    });
</script>

<style scoped>
    .filter-card {
        position: relative;
    }

    .reset-btn {
        min-width: 120px;
    }
</style>
