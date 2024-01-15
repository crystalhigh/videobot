<template>
  <div class="audio-recorder">
    <VueRecordAudio @result="onResult" :mode="'press'" />
    <h6 class="text-center">
      Press MIC to record an audio<br />You can record up to 2 minutes.
    </h6>
    <button
      type="button"
      class="btn"
      @click="onBack"
      v-if="mobile"
      :style="{
        backgroundColor: bgColor,
        color,
      }"
    >
      Back
    </button>
  </div>
</template>

<style lang="scss" scoped>
.audio-recorder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  h6 {
    margin-top: 20px;
  }
}
</style>

<script>
import { VueRecordAudio } from '@codekraft-studio/vue-record';
export default {
  components: {
    VueRecordAudio
  },
  props: {
    mobile: { type: Boolean, default: true },
    bgColor: { type: String, default: '#1998e4' },
    color: { type: String, default: '#ffffff' }
  },
  data() {
    return {};
  },
  methods: {
    onResult(data) {
      this.$emit('onResult', data);
    },
    onBack() {
      this.$emit('onBack');
    }
  }
};
</script>
