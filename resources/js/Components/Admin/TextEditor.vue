<template>
  <div>
    <div ref="editorContainer" style="min-height: 100px;"></div>
  </div>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits, onMounted } from "vue";
import Quill from "quill";

// Receive the v-model prop
const props = defineProps({
  modelValue: {
    type: String,
    default: "",
  },
});

// Define the emit function
const emit = defineEmits();

// Reference to the Quill editor container
const editorContainer = ref(null);
let editor;

// Initialize the Quill editor on mount
onMounted(() => {
  editor = new Quill(editorContainer.value, {
    theme: "snow",
    modules: {
      toolbar: [
        [{ header: "1" }, { header: "2" }, { font: [] }],
        [{ list: "ordered" }, { list: "bullet" }],
        ["bold", "italic", "underline"],
        ["link"],
        [{ align: [] }],
        ["image"],
      ],
    },
    placeholder: "Compose your text here...",
  });

  // Set the initial value of the editor
  editor.root.innerHTML = props.modelValue;

  // Listen for changes to the editor content
  editor.on("text-change", () => {
    emit("update:modelValue", editor.root.innerHTML);  // Emit the updated content
  });
});

watch(() => props.modelValue, (newVal) => {
  // Update the editor content if the parent updates the value
  if (editor && editor.root.innerHTML !== newVal) {
    editor.root.innerHTML = newVal;
  }
});
</script>
