<template>
  <div class="flex flex-col gap-10">
    <!-- The Packets -->
    <packets></packets>

    <!-- The Activities -->
    <activities></activities>
  </div>
</template>

<script>
import Packets from "../components/Packet/Packets.vue";
import Activities from "../components/Activity/Activities";

export default {
  components: {
    Packets,
    Activities,
  },
};
</script>