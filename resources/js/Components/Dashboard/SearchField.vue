<template>
  <div>
    <v-text-field
      v-model="searchValue"
      :label="label"
      prepend-inner-icon="mdi-magnify"
      single-line
      hide-details
      clearable
      variant="outlined"
      density="comfortable"
      @update:model-value="handleSearch"
      @click:clear="handleClear"
      :loading="loading"
    >
      <template v-slot:append>
        <v-fade-transition leave-absolute>
          <v-progress-circular
            v-if="loading"
            size="24"
            color="primary"
            indeterminate
          ></v-progress-circular>
        </v-fade-transition>
      </template>
    </v-text-field>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import debounce from 'lodash.debounce';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: 'Search'
  },
  debounceTime: {
    type: Number,
    default: 300
  },
  loading: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'search', 'clear']);

const searchValue = ref(props.modelValue);

// Watch for external changes
watch(() => props.modelValue, (newVal) => {
  searchValue.value = newVal;
});

// Handle search with debounce
const debouncedEmit = debounce((val) => {
  emit('search', val);
}, props.debounceTime);

const handleSearch = (val) => {
  emit('update:modelValue', val);
  debouncedEmit(val);
};

const handleClear = () => {
  emit('update:modelValue', '');
  emit('clear');
};
</script>
