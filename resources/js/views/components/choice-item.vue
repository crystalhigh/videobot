<template>
  <div class="choice-item">
    <div class="caption-text">
      <div class="content">
        <input type="text" v-model="item.title" />
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
.choice-item {
  margin-bottom: 15px;
  padding: 0px 5px;
  .caption-text {
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
        padding: .4rem 1em;
        width: 100%;
        border: 0px;
        background-color: #0000000d;
        border-radius: 5px;
        font-size: 16px;
        font-weight: 500;
        &:focus-visible {
          outline-color: #cfe2f3;
        }
      }
    }
    .remove {
      min-width: 0px;
      min-height: 0px;
      flex-shrink: 0;
      height: 100%;
      padding-left: 8px;
      button {
        color: #e73838;
        &:hover {
          color: #e27171;
        }
      }
    }
  }
}
</style>

<script>
export default {
  name: 'choice-item',
  props: { item: Object, default: {} },
  data() {
    return {};
  },
  mounted() {},
  methods: {
    handleRemove(item) {
      this.$emit('onRemoveChoiceItem', { item: item });
    }
  }
};
</script>
