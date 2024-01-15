<template>
	<div class="publisher-responses">
		<div class="flow-headers">
      <span :class="mode == 'All' ? 'active' : ''">
				<a @click="handleMode('All')">All</a>
			</span>
      <span :class="mode == 'Pending' ? 'active' : ''">
				<a @click="handleMode('Pending')">Pending</a>
			</span>
			<span :class="mode == 'Approved' ? 'active' : ''">
				<a @click="handleMode('Approved')">Approved</a>
			</span>
			<span :class="mode == 'Rejected' ? 'active' : ''">
				<a @click="handleMode('Rejected')">Rejected</a>
			</span>
    </div>
		<div class="publisher-responses-content">
			<responses-item :item="item" v-for="(item, index) in items" :key="`responses${index}`" @handleCopy="handleCopy"/>
		</div>
		<b-modal
      id="promote-tools-modal1"
      hide-footer
      title="Promote Tools"
      centered
      ref="promoteTollsModal"
      size="md">
      <div>
        <span class="promote-tools-modal-span fw-700">Widget Code:</span>
      </div>
      <div>
        <b-form-textarea
          v-model="widgetCode"
          readonly
          class="copy-code-input mt-2"
          style="font-size: 16px; border-radius: 4px;"
          rows="4"
          max-rows="4"
        />
      </div>
      <div class="d-flex mt-3 align-items-center gap-12">
        <span class="promote-tools-modal-span">Position:</span>
        <b-form-select
          v-model="widgetPosition"
          :options="widgetPositionOptions"
        ></b-form-select>
      </div>
      <div class="d-flex mt-3 align-items-center gap-12">
        <span class="promote-tools-modal-span">Widget Style:</span>
        <b-form-select
          v-model="widgetStyle"
          :options="widgetStyleOptions"
        ></b-form-select>
      </div>
      <div class="d-flex mt-3 align-items-center gap-12">
        <span class="promote-tools-modal-span">Background Color:</span>
        <input
          type="color"
          ref="colorRef"
          class="w-100"
          style="border-radius:4px; overflow: hidden;cursor: pointer;"
          v-model="widgetBorderColor"
        />
      </div>
      <div class="d-flex mt-3 align-items-center gap-12" style="position: relative;">
        <span class="promote-tools-modal-span">Text:</span>
        <b-form-input
          type="text"
          v-model="widgetText"
          placeholder="Enter your widget text."
          class="py-3"
          style="border-radius: 4px;"
					ref="widgetTextRef"
        />
				<a @click="showEmoji" class="emoji-icon"><fa-icon :icon="['fas', 'smile']" /></a>
      </div>
			<VEmojiPicker @select="selectEmoji" v-if="emoji" class="emoji-modal"/>
      <div class="mt-3">
        <span class="promote-tools-modal-span fw-700">OR:</span>
      </div>
      <div>
        <span class="promote-tools-modal-span fw-700">Your Direct Promote Link:</span>
      </div>
      <div>
        <b-form-input
          type="text"
          v-model="widgetLink"
          readonly
          class="mt-2 py-3"
          style="border-radius: 4px;"
        />
      </div>
      <div class="d-flex justify-content-end copy-modal">
        <b-button class="mt-3 px-2 text-center" variant="primary" @click="shareCode">Copy Code</b-button>
        <b-button class="mt-3 px-2 text-center ml-2" variant="primary" @click="shareLink">Copy Link</b-button>
        <b-button class="mt-3 ml-2 px-2" variant="danger" @click="hideCodeModal">Close</b-button>
      </div>
    </b-modal>
	</div>
  </template>
  
  <style lang="scss" scoped>
	.emoji-icon {
		color: orange;
		font-size: 20px;
		position: absolute;
		top: 3px;
		right: 8px;
		&:hover {
			cursor: pointer;
			opacity: .7;
		}
	}
	.emoji-modal {
			position: absolute;
			right: -330px;
			top: -62px;
		}
		.publisher-responses {
			padding: 30px;
			height: 100%;
			background-color: white;
			border-radius: 30px;
		}
		.promote-tools-modal-span {
			min-width: 140px;
			font-size: 16px;
		}
		.copy-code-input {
			border-radius: 16px;
			height: auto !important;
		}
		.fw-700 {
			font-weight: 700;
		}
		.gap-12 {
			gap: 12px;
		}
  </style>
  
  <script>
  import axios from 'axios';
	import { mapGetters } from 'vuex';
	import ResponsesItem from '@/views/components/responses-item';
	import { VEmojiPicker } from 'v-emoji-picker';
  export default {
    name: 'responses',
		components: {
			ResponsesItem, VEmojiPicker
		},
		computed: {
			...mapGetters(['currentUser'])
		},
		watch: {
			widgetPosition: function(newVal, oldVal) {
				this.setWidgetCode();
			},
			widgetStyle: function(newVal, oldVal) {
				this.setWidgetCode();
			},
			widgetBorderColor: function(newVal, oldVal) {
				this.setWidgetCode();
			},
			widgetText: function(newVal, oldVal) {
				this.setWidgetCode();
			},
		},
    data() {
      return {
				items: [],
				responsesItems: [],
				mode: 'All',
				awsUrl: 'https://vidpopup.s3.amazonaws.com/',
				widgetCode: '',
				widgetPosition: 'right',
				widgetPositionOptions: [
					{value: 'right', text: 'Right'},
					{value: 'left', text: 'Left'}
				],
				widgetStyle: 'circle',
				widgetStyleOptions: [
					{value: 'circle', text: 'Circle'},
					{value: 'v-rect', text: 'Square'}
				],
				widgetBorderColor: '#1998e4',
				widgetText: '',
				widgetLink: '',
				selectedData: [],
				emoji: false,
      };
    },
		beforeMount() {
			if (!this.currentUser || (this.currentUser && this.currentUser.role != 'publisher')) {
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
    mounted() {
			this.awsUrl = process.env.MIX_CDN_URL;
			this.loadResponses();
    },
    methods: {
			showEmoji() {
				this.emoji = !this.emoji;
			},
			selectEmoji(emoji) {
				const ref = this.$refs.widgetTextRef;
				const start = this.widgetText.substring(0, ref.selectionStart)
				const end = this.widgetText.substring(ref.selectionStart)
				this.widgetText = start + emoji.data + end;
				this.emoji = false;
			},
			async loadResponses() {
				const loader = this.$loading.show();
				try {
					const { data } = await axios.get('/api/publisher-response');
					this.items = data;
					this.responsesItems = data;
				} catch (err) {
					console.log('err =>', err);
					this.$func.makeToast(
						this,
						'Error',
						'Cannot load server data!',
						'danger'
					);
				}
				loader && loader.hide();
			},
			handleMode(mode) {
				this.mode = mode;
				if (mode == 'All')
					this.items = this.responsesItems;
				else
					this.items = this.responsesItems.filter(val => val.status == mode)
			},
			setWidgetCode() {
				this.widgetCode = `<link rel="stylesheet" href="${
            process.env.MIX_APP_URL
          }/embed/embed.css" />
          <script>
            window.VIDPOP_EMBED_CONFIG = {
              url: "${process.env.MIX_APP_URL}/live/${this.selectedData.vidgenCode}?vidgenID=${this.selectedData.pv_id}",
              thumb: "${this.getThumbUrl(this.selectedData.thumb)}",
              widget: "${this.widgetStyle}",
              option: {
                background: "${this.widgetBorderColor}",
								position: "${this.widgetPosition}",
								text: "${this.widgetText}",
              },
			  main: "${process.env.MIX_APP_URL}",
			  vidgenID: "${this.selectedData.pv_id}"
            }
          <\/script>
          <script src="${
            process.env.MIX_APP_URL
          }/embed/embed2.js"><\/script>`;
			},
			handleCopy(data) {
				this.selectedData = data;
				this.formatData();
				this.setWidgetCode();
				this.widgetLink = `${process.env.MIX_APP_URL}/live/${data.vidgenCode}?vidgenID=${data.pv_id}`
				this.$bvModal.show('promote-tools-modal1');
			},
			getThumbUrl(url) {
				if (url[0] == 'u') // uploaded file
					return `${this.awsUrl}${url}`;
				return url;
			},
			shareCode() {
				navigator.clipboard.writeText(this.widgetCode);
				this.$func.makeToast(
					this,
					'Notice',
					'Code is copied to clipboard',
					'success'
				);
				this.$bvModal.hide('promote-tools-modal1');
			},
			shareLink() {
				navigator.clipboard.writeText(this.widgetLink);
				this.$func.makeToast(
					this,
					'Notice',
					'Link is copied to clipboard',
					'success'
				);
				this.$bvModal.hide('promote-tools-modal1');
			},
			hideCodeModal() {
				this.$bvModal.hide('promote-tools-modal1');
			},
			formatData() {
				this.widgetPosition = 'right';
				this.widgetStyle = 'circle';
				this.widgetBorderColor = '#1998e4';
				this.widgetText = '';
				this.widgetLink = '';
			},
    }
  };
  </script>
  