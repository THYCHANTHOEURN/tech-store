<template>
    <v-container class="pa-0 py-4">
        <v-label v-if="label" :class="{ 'required': required }">{{ label }}</v-label>
        <div class="editor-container" :class="{ 'v-input--error': error }">
            <VuetifyTiptap v-model="internalValue" rounded :min-height="minHeight" :max-height="maxHeight"
                :max-width="maxWidth" :extensions="extensions" :disabled="disabled" @update:model-value="updateValue" />
        </div>
        <v-messages v-if="helpText && !error" :messages="[helpText]" class="mt-1"></v-messages>
        <v-messages v-if="error" :messages="[error]" color="error" class="mt-1"></v-messages>
    </v-container>
</template>

<script setup>
    import {
        BaseKit, Bold, Color, Fullscreen, Heading, Highlight, History,
        Image, Italic, Link, Strike, Table, Underline, Video,
        VuetifyTiptap, VuetifyViewer,
        BulletList, OrderedList, TextAlign
    } from 'vuetify-pro-tiptap'
    import 'vuetify-pro-tiptap/style.css'
    import { ref, watch, computed } from 'vue'

    const props = defineProps({
        modelValue: {
            type: String,
            default: ''
        },
        label: {
            type: String,
            default: ''
        },
        helpText: {
            type: String,
            default: ''
        },
        error: {
            type: String,
            default: ''
        },
        required: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        },
        placeholder: {
            type: String,
            default: 'Type something...'
        },
        minHeight: {
            type: Number,
            default: 200
        },
        maxHeight: {
            type: Number,
            default: 465
        },
        maxWidth: {
            type: Number,
            default: 900
        },
        toolbarOptions: {
            type: Array,
            default: () => [
                'bold', 'italic', 'underline', 'strike',
                'color', 'highlight', 'heading', 'link',
                'bulletList', 'orderedList',  'align',
                'image', 'video', 'table'
            ]
        }
    })

    const emit = defineEmits(['update:modelValue'])

    const internalValue = ref(props.modelValue)

    watch(() => props.modelValue, (newValue) => {
        internalValue.value = newValue
    })

    const updateValue = (value) => {
        emit('update:modelValue', value)
    }

    // Dynamically build extensions based on toolbarOptions
    const extensions = computed(() => {
        const allExtensions = []

        // Base kit is always included
        allExtensions.push(BaseKit.configure({
            placeholder: {
                placeholder: props.placeholder,
            }
        }))

        // Add requested extensions based on toolbarOptions
        if (props.toolbarOptions.includes('bold')) allExtensions.push(Bold)
        if (props.toolbarOptions.includes('italic')) allExtensions.push(Italic)
        if (props.toolbarOptions.includes('underline')) allExtensions.push(Underline)
        if (props.toolbarOptions.includes('strike')) allExtensions.push(Strike)
        if (props.toolbarOptions.includes('color')) allExtensions.push(Color)
        if (props.toolbarOptions.includes('highlight')) allExtensions.push(Highlight)
        if (props.toolbarOptions.includes('heading')) allExtensions.push(Heading)
        if (props.toolbarOptions.includes('link')) allExtensions.push(Link)
        if (props.toolbarOptions.includes('image')) allExtensions.push(Image)
        if (props.toolbarOptions.includes('video')) allExtensions.push(Video)
        if (props.toolbarOptions.includes('table')) allExtensions.push(Table)
        if (props.toolbarOptions.includes('bulletList')) allExtensions.push(BulletList)
        if (props.toolbarOptions.includes('orderedList')) allExtensions.push(OrderedList)
        if (props.toolbarOptions.includes('align')) {
            allExtensions.push(TextAlign.configure({
                types: ['paragraph'],
                alignments: ['left', 'center', 'right', 'justify'],
            }))
        }

        // Always include these core extensions
        allExtensions.push(Fullscreen)
        allExtensions.push(History)

        return allExtensions
    })
</script>

<style scoped>
    .required::after {
        content: " *";
        color: rgb(var(--v-theme-error));
    }

    .editor-container {
        border-radius: 4px;
        transition: 0.3s cubic-bezier(0.25, 0.8, 0.5, 1);
    }

    .v-input--error :deep(.vuetify-pro-tiptap-editor) {
        border: 1px solid rgb(var(--v-theme-error)) !important;
    }

    :deep(.vuetify-pro-tiptap-editor) {
        width: 100%;
        border: 1px solid rgba(var(--v-theme-on-surface), 0.38) !important;
    }

    :deep(.vuetify-pro-tiptap-editor:hover) {
        border: 1px solid rgba(var(--v-theme-on-surface), 0.86) !important;
    }

    :deep(.vuetify-pro-tiptap-editor.focus) {
        border: 1px solid rgb(var(--v-theme-primary)) !important;
        box-shadow: 0 0 0 2px rgba(var(--v-theme-primary), 0.25);
    }
</style>
