<template>
  <div id="com-countdown">
    <span>{{ countdown }}</span>
  </div>
</template>

<style lang="scss" scoped>
#com-countdown {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  background-color: #00000094;
  display: flex;
  align-items: center;
  justify-content: center;
  span {
    font-size: 8rem;
    color: white;
  }
}
</style>

<script>
export default {
  name: 'countdown',
  data() {
    return {
      countdown: 3
    };
  },
  mounted() {
    const tm = setInterval(() => {
      this.countdown -= 1;
      if (this.countdown < 0) {
        clearInterval(tm);
        this.$emit('done');
      }
    }, 1000);
  },
  methods: {}
};
</script>
