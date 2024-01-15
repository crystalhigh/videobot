<template>
  <div class="cc-item">
    <div class="caption-time">
      <label for="time-start" class="mb-0">Start</label>
      <div class="time">
        <input
          placeholder="mm:ss"
          type="text"
          v-model="item.start"
          pattern="^[0-5][0-9]:[0-5][0-9]$"
        />
      </div>
      <label for="time-end" class="mb-0 ml-2">End</label>
      <div class="time">
        <input
          placeholder="mm:ss"
          type="text"
          v-model="item.end"
          pattern="^[0-5][0-9]:[0-5][0-9]$"
        />
      </div>
    </div>
    <div class="caption-text">
      <div class="content">
        <input type="text" v-model="item.description" />
      </div>
      <div class="remove">
        <button
          type="button"
          @click="handleRemove(item)"
          class="btn circle-button"
        >
          <fa-icon :icon="['fas', 'trash']" />
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.cc-item {
  border: 1px dashed #3490dc;
  padding: 10px;
  margin-bottom: 10px;
  .caption-time {
    display: flex;
    flex-direction: row;
    -webkit-box-pack: start;
    justify-content: flex-start;
    margin-top: 0.5em;
    & > label {
      display: flex;
      -webkit-box-pack: center;
      justify-content: center;
      -webkit-box-align: center;
      align-items: center;
      text-align: center;
      margin-right: 8px;
      font-size: 15px;
      color: rgb(128, 128, 128);
    }
    .time {
      display: flex;
      flex-direction: row;
      -webkit-box-align: center;
      align-items: center;
    }
    input {
      width: 80px;
      margin-left: 10px;
      text-align: center;
      padding: 0.8em 0.5em;
      font-size: 14px;
      font-family: monospace;
      border: 0px;
      background-color: rgba(0, 0, 0, 0.05);
      border-radius: 5px;
    }
  }
  .caption-text {
    margin-top: 16px;
    display: flex;
    flex-flow: row nowrap;
    -webkit-box-align: center;
    align-items: center;
    .content {
      min-width: 0px;
      min-height: 0px;
      flex: 1 1 0%;
      height: 100%;
      & > input {
        padding: 0.8em;
        width: 100%;
        border: 0px;
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 5px;
        font-size: 16px;
        font-weight: 500;
      }
    }
    .remove {
      min-width: 0px;
      min-height: 0px;
      flex-shrink: 0;
      height: 100%;
      padding-left: 16px;
    }
  }
}
</style>

<script>
export default {
  name: 'cc-item',
  props: { item: Object, default: {} },
  data() {
    return {};
  },
  mounted() {},
  methods: {
    handleRemove(item) {
      this.$emit('onRemoveCCItem', { item: item });
    }
  }
};
</script>
