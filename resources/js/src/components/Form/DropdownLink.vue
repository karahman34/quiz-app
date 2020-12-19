<template>
  <div class="cursor-pointer text-gray-700 px-3 py-1 hover:bg-gray-200" @click="$emit('click', $event)">
    <!-- Slot -->
    <slot></slot>
  </div>
</template>