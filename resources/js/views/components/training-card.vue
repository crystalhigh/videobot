<template>
  <div class="training-card">
    <div class="video-wrapper">
      <div class="training-video">
        <iframe
          :src="info.video"
          frameborder="0"
          allow="autoplay; encrypted-media"
          allowfullscreen
        ></iframe>
      </div>
      <div class="training-card-text mt-2">
        <h5 class="training-card-text">
          {{ info.name }}
        </h5>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.training-card {
  text-align: center;
  .training-video {
    position: relative;
    padding-bottom: 70%;
    height: 0;
    iframe {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      border-radius: 20px;
      width: 100%;
    }
  }
  .training-card-text {
    padding: 0px 10px;
    overflow: hidden;
    h5 {
      font-weight: bold;
      overflow: hidden;
    }
    span {
      color: #777;
    }
  }

  &:hover {
    cursor: pointer;
    span {
      color: #1998e4;
      font-weight: 700;
      text-decoration: underline;
    }
  }
}
</style>

<script>
export default {
  name: 'training-card',
  props: { info: Object, default: {} },
  data() {
    return {};
  }
};
</script>
