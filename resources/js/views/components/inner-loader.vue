<template>
  <div class="inner-overlay">
    <img src="/images/icons/spinner.svg" />
  </div>
</template>

<style lang="scss" scoped>
.inner-overlay {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}
</style>

<script>
export default {
  name: 'inner-loader'
};
</script>
