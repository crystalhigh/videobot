<template>
  <div id="page-vidpopup-endscreen">
    <div
      class="endscreen-wrapper"
      :style="{
        backgroundColor: endBg,
        color: endColor
      }"
    >
      <div
        class="overlay-text"
        :style="{
          top: getPosition(selectedPosition.value),
          transform: selectedPosition.value === 1 ? 'translateY(-50%)' : 'unset'
        }"
      >
        <h1
          class="mb-3"
          :style="{
            fontSize: titleSize.value
          }"
        >
          {{ title }}
        </h1>
        <h3
          v-html="linebreak(content)"
          :style="{
            fontSize: contentSize.value
          }"
        ></h3>
        <button
          type="button"
          class="btn mt-2"
          v-if="customButtonActive && customTitle"
          :style="{
            color: customColor,
            backgroundColor: customBgColor
          }"
        >
          {{ customTitle }}
        </button>
      </div>
    </div>
    <div class="vidpopup-content">
      <div class="content-wrapper">
        <div class="space-between">
          <h5 class="mb-0">Font Style</h5>
          <b-dropdown
            block
            menu-class="w-100"
            no-caret
            toggle-class="answer-toggle"
          >
            <template #button-content>
              <div class="fontStyleBtn current">
                English (US)
                <fa-icon :icon="['fas', 'angle-down']" class="ml-2" />
              </div>
            </template>
            <b-dropdown-item-button
              v-for="(item, index) in fontStyles"
              :key="`font-style-${index}`"
            >
              <div class="fontStyleBtn">
                English (US)
              </div>
            </b-dropdown-item-button>
          </b-dropdown>
        </div>
        <div class="space-between mt-3">
          <h5 class="mb-0">Edit BG color</h5>
          <div class="d-flex align-items-center">
            <input type="color" ref="colorRef" v-model="endBg" />
          </div>
        </div>
        <div class="space-between mt-3">
          <h5 class="mb-0">Edit Text color</h5>
          <div class="d-flex align-items-center">
            <input type="color" ref="colorRef" v-model="endColor" />
          </div>
        </div>
        <div class="space-between mt-3 mb-4">
          <h5 class="mb-0">Text Position</h5>
          <b-dropdown
            block
            menu-class="w-100"
            no-caret
            toggle-class="answer-toggle"
          >
            <template #button-content>
              <div>
                {{ selectedPosition.name }}
                <fa-icon :icon="['fas', 'angle-down']" class="ml-2" />
              </div>
            </template>
            <b-dropdown-item-button
              v-for="(item, index) in textPositions"
              @click="selectedPosition = item"
              :key="`text-position-${index}`"
            >
              <div>
                {{ item.name }}
              </div>
            </b-dropdown-item-button>
          </b-dropdown>
        </div>
        <div class="space-between">
          <h5>Text Title</h5>
          <b-dropdown
            block
            menu-class="w-100"
            no-caret
            toggle-class="answer-toggle"
          >
            <template #button-content>
              <div>
                {{ titleSize.text }}
                <fa-icon :icon="['fas', 'angle-down']" class="ml-2" />
              </div>
            </template>
            <b-dropdown-item-button
              v-for="(item, index) in titleSizes"
              @click="titleSize = item"
              :key="`title-size-${index}`"
            >
              <div>
                {{ item.text }}
              </div>
            </b-dropdown-item-button>
          </b-dropdown>
        </div>
        <b-form-input v-model="title" />
        <div class="space-between">
          <h5 class="mt-4">Text Description</h5>
          <b-dropdown
            block
            menu-class="w-100"
            no-caret
            toggle-class="answer-toggle"
          >
            <template #button-content>
              <div>
                {{ contentSize.text }}
                <fa-icon :icon="['fas', 'angle-down']" class="ml-2" />
              </div>
            </template>
            <b-dropdown-item-button
              v-for="(item, index) in contentSizes"
              @click="contentSize = item"
              :key="`content-size-${index}`"
            >
              <div>
                {{ item.text }}
              </div>
            </b-dropdown-item-button>
          </b-dropdown>
        </div>
        <div class="description-wrapper">
          <b-form-textarea
            id="description-text"
            v-model="content"
            placeholder="Enter your Text here"
            rows="3"
          ></b-form-textarea>
        </div>
        <div class="space-between mt-3 mb-4">
          <h5 class="mb-0">Custom Button</h5>
          <b-form-checkbox
            v-model="customButtonActive"
            name="seo"
            switch
            size="lg"
          ></b-form-checkbox>
        </div>
        <div v-if="customButtonActive">
          <div class="space-between">
            <h5>Custom Title</h5>
            <div class="d-flex align-items-center">
              <input type="color" v-model="customColor" />
              <input type="color" class="ml-2" v-model="customBgColor" />
            </div>
          </div>
          <b-form-input v-model="customTitle" />
          <h5 class="mt-4">Custom Link</h5>
          <div class="description-wrapper">
            <b-form-group
              id="google-analytics"
              label-for="google-analytics-input"
            >
              <b-form-input
                id="google-analytics-input"
                v-model="customLink"
                type="text"
                class="borderless-text-field"
              ></b-form-input>
            </b-form-group>
          </div>
        </div>
        <div class="row w-100 ml-0 mr-0">
          <div class="col-lg-12 justify-content-end pl-0 pr-0">
            <button
              type="button"
              class="btn btn-primary circle-button w-100"
              @click="back()"
            >
              Back
            </button>
          </div>
        </div>
        <div class="row w-100 ml-0 mr-0 mt-2">
          <div class="col-lg-12 justify-content-end pl-0 pr-0">
            <button
              type="button"
              class="btn btn-primary circle-button w-100"
              @click="saveVidpop()"
            >
              <fa-icon :icon="['fas', 'thumbs-up']" /> Done
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
input[type='color'] {
  -webkit-appearance: none;
  border: none;
  width: 32px;
  height: 32px;
  display: flex;
}
input[type='color']::-webkit-color-swatch-wrapper {
  padding: 0;
}
input[type='color']::-webkit-color-swatch {
  border: none;
}
#page-vidpopup-endscreen {
  border-radius: 30px;
  height: 100%;
  display: flex;
  overflow: hidden;
  .endscreen-wrapper {
    width: 50%;
    border-top-left-radius: 30px;
    border-bottom-left-radius: 30px;
    overflow: hidden;
    position: relative;
    text-align: center;
    .overlay-text {
      position: absolute;
      text-align: center;
      width: 100%;
    }

    @media (min-width: 1500px) {
      width: 70%;
    }
  }
  .vidpopup-content {
    width: 50%;
    background-color: white;
    border-top-right-radius: 30px;
    border-bottom-right-radius: 30px;
    padding: 100px 20px 20px 20px;
    @media (min-width: 1500px) {
      width: 30%;
      padding: 40px 30px 30px 30px;
    }

    .content-wrapper {
      overflow-y: auto;
      padding: 0px 10px 50px 5px;

      .description-wrapper {
        textarea {
          background-color: #f0f1f3;
          padding: 1rem;
          resize: none;
          border-radius: 0.5rem;
          border: none;
        }
      }

      .fontStyleBtn {
        width: 100%;
        -webkit-box-pack: justify;
        justify-content: space-between;
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        background: transparent;
        height: 41px;
        padding: 0px 20px 0px 20px;
        border: none;
        font-weight: 500;
        font-size: 16px;
        outline: none;
        transition: all 0.1s ease-in-out 0s;
      }
      .current {
        background: #f0f0f0;
        border-radius: 25px;
      }

      .selected-pane {
        width: 35px;
        height: 35px;
        border-radius: 100%;
        background: #1998e4;
      }
    }
  }
  .dropdown-toggle:focus {
    box-shadow: none !important;
  }
}
</style>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
import { VIDPOP } from '@/services/store/vidpopup.module';
export default {
  name: 'vidpopup-endscreen',
  computed: {
    ...mapGetters(['vidpop', 'currentUser'])
  },
  data() {
    return {
      vidpop_id: '',
      id: '',
      fontStyles: [{}],
      selectedPosition: { name: 'Top', value: 0 },
      textPositions: [
        { name: 'Top', value: 0 },
        { name: 'Center', value: 1 },
        { name: 'Bottom', value: 2 }
      ],
      endBg: '#eeeeee',
      endColor: '#ffffff',
      title: '',
      content: '',
      customLink: '',
      customTitle: '',
      customButtonActive: false,
      isMounted: false,
      titleSize: { value: '2.5rem', text: 'Medium' },
      titleSizes: [
        { value: '1.5rem', text: 'Extra Small' },
        { value: '2rem', text: 'Small' },
        { value: '2.5rem', text: 'Medium' },
        { value: '3rem', text: 'Large' },
        { value: '3.5rem', text: 'Extra Large' }
      ],
      contentSize: { value: '1.5rem', text: 'Medium' },
      contentSizes: [
        { value: '0.75rem', text: 'Extra Small' },
        { value: '1rem', text: 'Small' },
        { value: '1.5rem', text: 'Medium' },
        { value: '1.75rem', text: 'Large' },
        { value: '2rem', text: 'Extra Large' }
      ],
      customColor: '#ffffff',
      customBgColor: '#1998e4'
    };
  },
  beforeMount() {
    if (!this.currentUser || (this.currentUser && this.currentUser.role != 'agent')) {
      this.$func.makeToast(
        this,
        'Error',
        'You are not allowed to visit this page!',
        'danger'
      );
      if (this.currentUser.role == 'admin')
        this.$router.push({path: '/app/admin-payout'});
      else
        this.$router.push({path: '/app/statistics'});
    }
  },
  watch: {
    endBg: function(newVal, oldVal) {
      if (!this.isMounted) return;
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        end_bg: newVal
      });
    },
    endColor: function(newVal, oldVal) {
      if (!this.isMounted) return;
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        end_color: newVal
      });
    },
    title: function(newVal, oldVal) {
      if (!this.isMounted) return;
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        title: newVal
      });
    },
    content: function(newVal, oldVal) {
      if (!this.isMounted) return;
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        content: newVal
      });
    },
    customButtonActive: function(newVal, oldVal) {
      if (!this.isMounted) return;
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        is_custom: newVal
      });
    },
    customTitle: function(newVal, oldVal) {
      if (!this.isMounted) return;
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        custom_text: newVal
      });
    },
    customLink: function(newVal, oldVal) {
      if (!this.isMounted) return;
      this.$store.dispatch(VIDPOP, {
        ...this.vidpop,
        custom_link: newVal
      });
    },
    selectedPosition: {
      handler: function(newVal, oldVal) {
        if (newVal && !this.isMounted) {
          this.$store.dispatch(VIDPOP, {
            ...this.vidpop,
            end_position: newVal.value
          });
        }
      },
      deep: true
    }
  },
  async mounted() {
    this.vidpop_id = this.$route.params.id;
    // load vidpop
    const loader = this.$loading.show();
    axios
      .get(`/api/vidpop/${this.vidpop_id}`)
      .then(res => {
        if (res.status === 200) {
          const data = res.data.vidpop;
          this.$store.dispatch(VIDPOP, data);
          this.endBg = data.end_bg || '#000000';
          this.endColor = data.end_color || '#FFFFFF';
          this.title = data.title;
          this.content = data.content;
          this.customButtonActive = data.is_custom === 1 ? true : false;
          this.customTitle = data.custom_text;
          this.customLink = data.custom_link;
          const usedPos = this.textPositions.find(
            x => x.value == data.end_position
          );
          if (usedPos) {
            this.selectedPosition = usedPos;
          }
          this.customColor = data.custom_color;
          this.customBgColor = data.custom_bgcolor;

          const usedTitleSize = this.titleSizes.find(
            x => x.value === data.title_size
          );
          if (usedTitleSize) {
            this.titleSize = usedTitleSize;
          }

          const usedContentSize = this.contentSizes.find(
            x => x.value === data.content_size
          );
          if (usedContentSize) {
            this.contentSize = usedTitleSize;
          }

          this.isMounted = true;
        } else {
          // show invalid screen
        }
      })
      .catch(err => {})
      .finally(() => {
        loader && loader.hide();
      });
  },

  methods: {
    getPosition(pos) {
      if (pos === 0) {
        return '20%';
      } else if (pos === 1) {
        return '45%';
      } else if (pos === 2) {
        return '70%';
      }
    },
    saveVidpop() {
      // save vidpop
      const loader = this.$loading.show();
      axios
        .post(`/api/vidpop`, {
          ...this.vidpop,
          is_custom: this.customButtonActive === true ? 1 : 0,
          custom_text: this.customTitle,
          custom_link: this.customLink,
          end_bg: this.endBg,
          end_color: this.endColor,
          end_position: this.selectedPosition.value,
          title: this.title,
          title_size: this.titleSize.value,
          content: this.content,
          content_size: this.contentSize.value,
          custom_color: this.customColor,
          custom_bgcolor: this.customBgColor
        })
        .then(res => {
          if (res && res.status === 200) {
            this.$func.makeToast(
              this,
              'Notice',
              'VidGen is successfully updated!',
              'success'
            );
          } else {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot save the VidGen!',
              'danger'
            );
          }
        })
        .catch(err => {
          this.$func.makeToast(
            this,
            'Error',
            'Cannot save the VidGen!',
            'danger'
          );
        })
        .finally(() => {
          loader && loader.hide();
        });
    },
    linebreak(text) {
      if (text) {
        return text.replace(/\n/g, '<br />');
      }
      return '';
    },
    back() {
      this.$router.go(-1);
    }
  }
};
</script>
