<template>
	<div class="publisher-payout pt-4 px-3">
		<h3>Payment Distributions</h3>
		<span class="publisher-payout-subtitle">Manage your payouts and commissions</span>
		<div class="d-flex align-items-center justify-content-between flex-wrap gap-12 mt-2">
			<div class="publisher-payout-card shadow rounded p-3">
				<h6>Commissions Earned</h6>
				<div><span class="publisher-payout-money">${{ earned }}</span></div>
			</div>
			<div class="publisher-payout-card shadow rounded p-3">
				<h6>Commissions Paid</h6>
				<div><span class="publisher-payout-money">${{ paid }}</span></div>
			</div>
			<div class="publisher-payout-card shadow rounded p-3">
				<h6>Current Balance</h6>
				<div><span class="publisher-payout-money">${{ balance }}</span></div>
			</div>
			<div class="publisher-payout-card shadow rounded p-3">
				<h6>Available for distribution</h6>
				<div><span class="publisher-payout-money">${{ available }}</span></div>
			</div>
		</div>
		<h6 class="mt-4">To Withdraw You Most Met The following Vid-Gen Requirements:</h6>
		<ul>
			<li>
				The Vid-Gen payment cycle is monthly. You accrue estimated earnings over the course of a month, and then at the beginning of the following month your earnings are finalized and posted to your Available for distribution balance.
			</li>
			<li>
				You have over $10,000.00 available for distribution.
			</li>
			<li>
				You have selected a Vid-Gen Distribution Method.
			</li>
		</ul>
		<div>
			<span>
				To withdraw money insert the amount you'd like to withdraw:
			</span>
		</div>
		<b-form-input
			type="text"
			required
			v-model="withdrawMoneyAmount"
			class="mt-3"
		></b-form-input>
		<b-button class="text-center mt-3" variant="primary" @click="onWithdrawMoney" :disabled="available < 10000">Withdraw money</b-button>
		<div v-if="last_distribution">
			<div class="mt-3">
				<span>
					Last commission distribution of ${{ last_distribution }} was transferred on {{ last_distribution_date }}.
				</span>
			</div>
			<div class="mt-2">
				<a class="btn btn-distribution-detail" @click="getDistributionData">Details</a>
			</div>
			<ul v-if="detail_show">
				<li v-for="(val, index) in detail" :key="index">{{ val }}</li>
			</ul>
		</div>
	</div>
</template>
  
<style lang="scss" scoped>
	.btn-distribution-detail {
		padding: 0;
		color:#1998e4;
		&:hover {
			text-decoration: underline;
		}
	}
	.publisher-payout-subtitle {
		font-size: 20px;
	}
	@media (max-width: 768px) {
		.publisher-payout-card {
			min-height: 120px;
		}	
	}
	.publisher-payout-card {
		flex: 1;
	}
	.publisher-payout-money {
		font-size: 24px;
		font-weight: 700;
	}
	.gap-12 {
		gap: 12px;
	}
</style>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
export default {
	name: 'payout',
	data() {
		return {
			withdrawMoneyAmount: 0,
			earned: 0,
			paid: 0,
			balance: 0,
			available: 0,
			last_distribution: 0,
			last_distribution_date: null,
			detail_show: false,
			detail: [],
		};
	},
	computed: {
    ...mapGetters(['currentUser'])
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
		this.loadPaymentData();
	},
	methods: {
		async loadPaymentData () {
			try {
				const res = await axios.get('/api/payout');
				if (res && res.status == 200) {
					this.earned = res.data.earned;
					this.paid = res.data.paid;
					this.available = res.data.available;
					this.balance = res.data.balance;
					if (res.data.last_distribution) {
						this.last_distribution = res.data.last_distribution.withdraw;
						this.last_distribution_date = this.getFormattedDate(res.data.last_distribution.created_at);
					}
				}
			} catch (err) {
				console.log('err =>', err);
        this.$func.makeToast(
          this,
          'Error',
          'Cannot load server data!',
          'danger'
        );
			}
		},
		getDistributionData() {
			this.detail_show = !this.detail_show
			this.detail = [];
			axios.get('/api/payout-distribution')
			.then(res => {
				if (res && res.status == 200) {
					res.data.map(val => {
						this.detail = [...this.detail, `$${val.withdraw} was transferred on ${this.getFormattedDate(val.updated_at)}`];
					})
				}
			})
			.catch (err => {
			console.log('err =>', err);
			this.$func.makeToast(
				this,
				'Error',
				'Cannot load server data!',
				'danger');
			})
		},
		onWithdrawMoney() {
			// if (this.available < 100) {
			// 	this.$func.makeToast(
      //     this,
      //     'Warning',
      //     'The money avalable for distribution is less than $100!',
      //     'danger'
      //   );
			// 	return;
			// }
			let regex = /^(?!0)([0-9]*[.])?[0-9]+$/;
			if (!regex.test(this.withdrawMoneyAmount)) {
				this.$func.makeToast(
          this,
          'Warning',
          'Insert the amount to withdraw money correctly!',
          'danger'
        );
				return;
			}
			if (this.withdrawMoneyAmount > this.available) {
				this.$func.makeToast(
          this,
          'Warning',
          'Amount to withdraw money is not available to distribute!',
          'danger'
        );
				return;
			}
			this.$bvModal.msgBoxConfirm(`Are you sure want to withdraw $${this.withdrawMoneyAmount}?`)
			.then(val => {
				if (val) {
					const loader = this.$loading.show();
					axios.post('/api/payout', {
						withdraw: this.withdrawMoneyAmount
					})
					.then(res => {
						if (res && res.status === 200) {
							this.$func.makeToast(this, 'Success', 'Sent withdraw request to server!', 'success');
							this.balance = res.data.balance;
							this.available = res.data.available;
							this.last_distribution = parseFloat(res.data.payout.withdraw);
							this.last_distribution_date = this.getFormattedDate(res.data.payout.created_at);
							this.withdrawMoneyAmount = 0;
						}
					})
					.catch(err => {
						console.log('err =>', err);
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
				}
			})
			.catch(err => {
			})
		},
		getFormattedDate(date) {
			if (date) {
				let tmp = new Date(date);
				return `${tmp.getDate()}/${tmp.getMonth() + 1}/${tmp.getFullYear()}`;
			} else
				return '';
		}
	}
};
</script>
  