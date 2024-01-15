<template>
	<div class="advertiser-requests">
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
		<div class="advertiser-requests-content">
			<requests-item :item="item" v-for="(item, index) in items" :key="`requests${mode}${index}`" @handleStatus="handleStatus" @handlePublisherStatus="handlePublisherStatus"/>
		</div>
	</div>
  </template>
  
  <style lang="scss" scoped>
		.advertiser-requests {
			padding: 30px;
			height: 100%;
			background-color: white;
			border-radius: 30px;
		}
  </style>
  
  <script>
  import axios from 'axios';
	import { mapGetters } from 'vuex';
	import RequestsItem from '@/views/components/requests-item';
	import { ADV_REQUEST_COUNT } from '@/services/store/vidpopup.module';
  export default {
    name: 'requests',
		components: {
			RequestsItem
		},
		computed: {
			...mapGetters(['currentUser'])
		},
    data() {
      return {
				mode: 'All', // 0: all, 1: pending, 2: approved, 3: rejected
				requestsItems: [],
				items: [],
      };
    },
		beforeMount() {
			if (!this.currentUser || (this.currentUser && this.currentUser.role != 'advertiser' && this.currentUser.role != 'origin')) {
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
			this.loadRequests();
    },
    methods: {
			async loadRequests() {
				const loader = this.$loading.show();
				const [requests, bans] = await Promise.all([
					axios.get('/api/advertiser-request'),
					// axios.get('/api/publisher-ban'),
				])
				.catch(err => {
					console.log('err => ', err)
					this.$func.makeToast(
						this,
						'Error',
						'Cannot load server data!',
						'danger'
					);
				})
				.finally(() => {
					loader && loader.hide();
				});
				if (requests && requests.status == 200) {
					this.requestsItems = requests.data;
					this.items = requests.data;
				}
			},
			handleMode(mode) {
				this.mode = mode;
				if (mode == 'All')
					this.items = this.requestsItems;
				else
					this.items = this.requestsItems.filter(val => val.status == mode)
			},
			handleStatus(data) {
				axios.put('/api/publisher-vidpopup/' + data.id, { status: data.status })
				.then(res => {
					if (res && res.status == 200) {
						this.$func.makeToast(
							this,
							'Notice',
							data.status + ' this vidgen!',
							'success'
						);
						let item = this.requestsItems.filter(val => val.id == data.id)[0];
						item.status = data.status;
						if (this.mode != 'All')
							this.items = this.requestsItems.filter(val => val.status == this.mode)
						let advRequestCount = res.data.advRequestCount;
						this.$store.dispatch(ADV_REQUEST_COUNT, advRequestCount);
					}
				})
				.catch(err => {
					console.log('err =>', err);
					this.$func.makeToast(
						this,
						'Error',
						'Cannot update status!',
						'danger'
					);
				})
			},
			handlePublisherStatus(data) {
				if (data.status == 0) { // ban
					axios.post('/api/publisher-ban', {
						publisher_id: data.publisher_id
					})
					.then(res => {
						if (res && res.status == 200) {
							if (res.data.error) {
								this.$func.makeToast(
									this,
									'Notice',
									res.data.error,
									'danger'
								);
							} else {
								this.$func.makeToast(
									this,
									'Notice',
									'Banned publisher!',
									'success'
								);
							}
							this.items.map(val => {
								if (val.creator_id == res.data.advertiser_id && val.publisher_id == res.data.publisher_id) {
									val.publisher_ban.push(res.data);
									val.status = "Rejected";
								}
							});
							if (this.mode != 'All')
								this.items = this.items.filter(val => val.status == this.mode)
						}
					})
					.catch(err => {
						console.log('err =>', err);
						this.$func.makeToast(
							this,
							'Error',
							'Cannot create banned publisher!',
							'danger'
						);
					})
				} else { // agree
					axios.delete('/api/publisher-ban/' + data.deleteId)
					.then(res => {
						if (res && res.status == 200) {
							if (res.data.error) {
								this.$func.makeToast(
									this,
									'Notice',
									res.data.error,
									'danger'
								);
							} else {
								this.$func.makeToast(
									this,
									'Notice',
									'Agreed publisher!',
									'success'
								);
							}
							this.items.map(val => {
								let index = val.publisher_ban.findIndex(val => val.id == data.deleteId);
								if (index != -1)
									val.publisher_ban.splice(index, 1);
								if (val.creator_id == data.advertiser_id && val.publisher_id == data.publisher_id) {
									val.publisher_ban.push(res.data);
									val.status = "Pending";
								}
							});
							if (this.mode != 'All')
								this.items = this.items.filter(val => val.status == this.mode)
						}
					})
					.catch(err => {
						console.log('err =>', err);
						this.$func.makeToast(
							this,
							'Error',
							'Cannot delete banned publisher!',
							'danger'
						);
					})
				}
			}
    }
  };
  </script>
  