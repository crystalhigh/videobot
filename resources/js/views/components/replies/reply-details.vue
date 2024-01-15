<template>
  <div id="com-reply-detail">
    <h5 class="d-flex justify-content-end">{{ responder.replies[0].publisher_vidpopup.publisher.name }}</h5>
    <h3 class="vidpop-title">
      VidGen : {{ responder.vidpop.name }}
      <span class="vidpop-badge" @click="gotoVidpop">
        <fa-icon :icon="['fas', 'external-link-alt']" />
      </span>
    </h3>
    <b-tabs class="h-100">
      <b-tab
        v-for="(reply, idx) in responder.replies"
        :key="`reply-step-${idx}`"
        :title="`Step${reply.step.index}`"
        class="h-100"
      >
        <div class="step-wrapper">
          <div class="step-logo-wrapper">
            <h4 v-if="reply.step.overlay.text">
              Overlay Text : {{ reply.step.overlay.text }}
            </h4>
          </div>
          <div class="step-reply video-wrapper" v-if="reply.type === 'video'">
            <video loop controls :key="reply.data">
              <source :src="`${awsUrl}${reply.data}`" :type="getType(reply.data)" />
            </video>
          </div>
          <div
            class="step-reply multiple-wrapper"
            v-else-if="reply.type === 'multiple'"
          >
            <div
              class="reply-multiple-item"
              v-for="(choice, idx1) in reply.step.answer.content"
              :key="`multiple-choice-${idx1}`"
              :class="isActiveMultiple(idx1, reply) ? 'active' : ''"
            >
              <span>{{ getLetter(idx1) }}</span
              >{{ choice.title }}
            </div>
          </div>
          <div class="step-reply" v-else>
            <h5>
              <span class="reply-tr">Answer Type :</span>
              <span class="reply-td text-capitalize">{{ reply.type }}</span>
            </h5>
            <div v-if="reply.type === 'audio' && reply.data">
              <audio controls>
                <source :src="`${awsUrl}${reply.data}`" type="audio/mpeg">
              </audio>
            </div>
            <!-- type button -->
            <div v-if="reply.type === 'button'">
              <h5>
                <span class="reply-tr">Answer Title : </span>
                <span class="reply-td">{{ reply.step.answer.content }}</span>
              </h5>
              <h5>
                <span class="reply-tr">Answer Url : </span>
                <a class="reply-td" :href="reply.step.answer.option.link">{{
                  reply.step.answer.option.link
                }}</a>
              </h5>
            </div>
            <!-- type payment -->
            <div v-if="reply.type === 'payment'">
              <h5>
                <span class="reply-tr">Pay Amount : </span>
                <span class="reply-td"
                  >${{ reply.step.answer.option.value }}</span
                >
              </h5>
              <h5>
                <span class="reply-tr">Answer Url : </span>
                <a class="reply-td" :href="reply.step.answer.content">{{
                  reply.step.answer.content
                }}</a>
              </h5>
            </div>
            <h5 v-if="reply.type === 'button' || reply.type === 'payment'">
              <span class="reply-tr">Clicked ?</span>
              <fa-icon :icon="['fas', 'check']" class="reply-td text-success" />
            </h5>
            <h5 v-if="reply.type === 'text'">
              <span class="reply-tr">Answer Text : </span>
              <span class="reply-td">{{ reply.data }}</span>
            </h5>
          </div>

          <div class="vidpop-split-line">
            <hr />
          </div>
          <div class="contact-details">
            <div>
              <h4>{{ responderName() }}</h4>
              <p class="date">{{ formatDate() }}</p>
              <a
                v-if="responder.autoResponder"
                :href="`mailto:${responder.autoResponder.email}`"
                >{{ responder.autoResponder.email }}</a
              >
            </div>
            <a
              v-if="responder.autoResponder"
              class="btn btn-reply"
              :href="`mailto:${responder.autoResponder.email}`"
            >
              Reply <fa-icon :icon="['fas', 'reply']" class="ml-2" />
            </a>
          </div>
        </div>
      </b-tab>
    </b-tabs>
  </div>
</template>

<style lang="scss" moduled>
$green-color: #36cf71;
$base-color: #63b3e2;
$base-light-color: #1998e4;
#com-reply-detail {
  padding: 20px;
  position: relative;
  height: 100%;
  background-color: white;
  display: flex;
  flex-direction: column;
  border-radius: 30px;
  .vidpop-title {
    display: flex;
    justify-content: center;
    .vidpop-badge {
      font-size: 0.75rem;
      color: $base-color;
      margin-left: 5px;
      cursor: pointer;
      &:hover {
        color: $base-light-color;
      }
    }
  }
  .nav-tabs {
    .nav-link {
      color: #777;
      font-weight: bold;
      &.active,
      &:hover,
      &:focus {
        color: $base-color;
        border-color: #ffffff #ffffff $base-color #ffffff !important;
      }
      &.active {
        border-width: 2px;
      }
    }
  }
  .tab-content {
    height: 100%;
  }
  .step-wrapper {
    padding: 30px;
    padding-bottom: 50px;
    height: 100%;
    display: flex;
    flex-direction: column;

    .step-logo-wrapper {
      display: flex;
      align-items: center;
      .logo {
        position: relative;
        img {
          width: 40px;
          height: 40px;
          border-radius: 10px;
        }
        .index-badge {
          position: absolute;
          border-radius: 50%;
          width: 20px;
          height: 20px;
          display: flex;
          align-items: center;
          justify-content: center;
          top: 0;
          left: 0;
          background-color: $base-color;
          color: white;
          transform: translate(-40%, -40%);
        }
      }
      .step-text {
        margin-left: 20px;
        font-size: 1.5rem;
        font-weight: 500;
      }
    }
    .step-reply {
      flex: 1;
      padding: 30px;
      font-size: 1.2rem;
      &.video-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
      }
      video {
        width: 50%;
      }
      h5 {
        margin-bottom: 1rem;
        display: flex;
        .reply-tr {
          width: 120px;
          color: #777;
          margin-right: 20px;
        }
        .reply-td {
          color: $base-color;
        }
      }
      &.multiple-wrapper {
        padding: 30px;
        display: flex;
        align-items: center;
        flex-direction: column;
        .reply-multiple-item {
          width: 350px;
          background-color: #0000000d;
          text-align: left;
          font-weight: 700;
          margin-bottom: 15px;
          border-radius: 30px;
          padding: 10px 30px;
          display: flex;
          align-items: center;
          font-size: 1rem;
          span {
            border-radius: 50%;
            background-color: $base-color;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            width: 30px;
            height: 30px;
            margin-right: 10px;
          }
          &.active {
            border: 2px solid $base-color;
          }
        }
      }
    }
    .contact-details {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0px 15px;
      .date {
        font-size: 0.8rem;
        color: #a1a1a1;
        margin-bottom: 0px;
      }
      a {
        font-weight: 600;
        font-size: 1.1rem;
        text-decoration: none;
      }
      .btn-reply {
        padding: 7px 20px;
        border-radius: 20px;
        color: white;
        font-size: 1.2rem;
        background-color: #1998e4;
      }
    }
    .vidpop-split-line {
      display: flex;
      align-items: center;
      hr {
        flex: 1;
        margin-left: 10px;
        border-top: 2px solid #d8ebfb;
      }
    }
  }
}
</style>

<script>
export default {
  name: 'reply-details',
  props: {
    responder: { type: Object, default: {} }
  },
  data() {
    return {
      awsUrl: 'https://vidpopup.s3.amazonaws.com/'
    };
  },
  mounted() {
    this.awsUrl = process.env.MIX_CDN_URL;
  },
  methods: {
    responderName() {
      if (this.responder.autoResponder) {
        return `${this.responder.autoResponder.name} ${this.responder.autoResponder.name1}`;
      }
      return '';
    },
    formatDate() {
      return this.$func.formatDate(this.responder.replies[0].created_at);
    },
    getLetter(idx) {
      return String.fromCharCode(65 + idx);
    },
    isActiveMultiple(idx, reply) {
      return parseInt(reply.data) === idx;
    },
    gotoVidpop() {
      this.$router.push({
        path: `/app/vidgen/${this.responder.vidpop.id}/settings/advance`
      });
    },
    getType(url) {
      if (url.endsWith('webm')) {
        return 'video/webm'
      }
      return 'video/mp4';
    }
  }
};
</script>
