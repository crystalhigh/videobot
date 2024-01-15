<template>
  <Container
    @drop="onDrop"
    @drag-enter="dragging = true"
    @drag-start="dragging = true"
    @drag-end="dragging = false"
    orientation="horizontal"
  >
    <Draggable v-for="(step, idx) in steps" :key="step.id">
      <div class="diagram-item-wrapper">
        <div class="diagram-item">
          <div class="diagram-img-wrapper">
            <img
              :src="getThumbUrl(step.thumb_url)"
              onerror="this.onerror=null;this.src='/images/logo-new1.png';"
            />
          </div>
          <span class="step-badge">{{ step.index }}</span>
          <span class="step-badge btn-edit-step" @click="editStep(step)" v-if="role == 'agent'">
            <fa-icon :icon="['fas', 'edit']" />
          </span>
        </div>
        <div class="v-line-wrapper">
          <hr v-if="!dragging && idx < steps.length - 1" />
          <span
            class="btn-plus"
            @click="addStep(step.index)"
            v-if="!dragging && idx < steps.length - 1"
          >
            <fa-icon :icon="['fas', 'arrow-right']" />
          </span>
        </div>
      </div>
    </Draggable>
    <!-- <div class="diagram-item-wrapper">
      <div class="diagram-item">
        <div class="diagram-end-wrapper">
          <h5>
            End<br />
            Scene
          </h5>
        </div>
        <span class="step-badge btn-edit-step" @click="editStep('end')">
          <fa-icon :icon="['fas', 'edit']" />
        </span>
      </div>
    </div> -->
  </Container>
</template>

<style lang="scss" moduled>
$base-color: #3490dc;
$badge-color: #546acc;
.diagram-item-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  .diagram-item {
    width: 150px;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(25, 152, 228, 0.1);
    border-radius: 50%;
    cursor: pointer;
    user-select: none;
    position: relative;
    .diagram-img-wrapper {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      overflow: hidden;
      img {
        max-width: unset !important;
        height: 100%;
        user-select: none;
        pointer-events: none;
        min-width: 100%;
      }
    }
    .step-badge {
      position: absolute;
      top: 10px;
      left: 10px;
      background-color: $badge-color;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      border-radius: 50%;
      font-size: 14px;
      font-weight: 700;
    }
    .btn-edit-step {
      right: 10px;
      bottom: 10px;
      left: unset;
      top: unset;
      background-color: $base-color;
      &:hover {
        filter: brightness(130%);
      }
    }
    .diagram-end-wrapper {
      width: 100px;
      height: 100px;
      background-color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
  }
  .v-line-wrapper {
    width: 100px;
    position: relative;
    hr {
      width: 100%;
      border-top: 2px solid black;
    }
    span {
      position: absolute;
      top: 0;
      left: 50%;
      transform: translate(-50%, 20%);
      width: 24px;
      height: 24px;
      background-color: black;
      border-radius: 30px;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      cursor: pointer;
      &:hover {
        background-color: #444;
        color: #eee;
      }
    }
  }
}
.smooth-dnd-ghost .diagram-item-wrapper .v-line-wrapper hr,
.smooth-dnd-ghost .diagram-item-wrapper .v-line-wrapper span {
  display: none !important;
}
</style>

<script>
import { Container, Draggable } from 'vue-smooth-dnd';

export default {
  name: 'diagram-viewer',
  components: {
    Container,
    Draggable
  },
  props: {
    steps: { type: Array, default: [] },
    vidpopId: { type: String, default: '' },
    role: { type: String, default: '' },
  },
  data() {
    return {
      dragging: false,
      awsUrl: 'https://vidpopup.s3.amazonaws.com/'
    };
  },
  mounted() {
    this.awsUrl = process.env.MIX_CDN_URL;
  },
  methods: {
    onDrop(dropResult) {
      const { removedIndex, addedIndex } = dropResult;
      if (removedIndex === addedIndex) {
        return;
      }
      // update index
      this.$emit('onUpdateIndex', {
        origin: removedIndex,
        updated: addedIndex
      });
    },
    addStep(idx) {
      this.$emit('onAddStep', idx);
    },
    editStep(step) {
      this.$router.push({
        path: `/app/vidgen/${step.vidpop_id}/${step.id}/edit/overlay`
      });
    },
    getThumbUrl(url) {
      if (url[0] == 'u') // uploaded file
        return `${this.awsUrl}${url}`;
      return url;
    }
  }
};
</script>
