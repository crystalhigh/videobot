<template>
  <div class="admin-payout">
    <div class="flow-headers">
      <span :class="mode == 'publisher' ? 'active' : ''">
        <a @click="handleMode('publisher')">Publisher List</a>
      </span>
      <span :class="mode == 'advertiser' ? 'active' : ''">
        <a @click="handleMode('advertiser')">Advertiser List</a>
      </span>
    </div>
    <div v-if="mode == 'publisher'">
      <b-table
				id="admin-payout-publisher-table"
				hover
				bordered
				sticky-header
				head-variant="light"
				:fields="publisherFields"
				:items="publisherItems"
				class="w-100 text-center"
			>
				<template #cell(index)="row">
					<span>{{ row.index + 1 }}</span>
				</template>
        <template #cell(earned)="row">
					<span>{{ `$${row.value}` }}</span>
				</template>
        <template #cell(paid)="row">
					<span>{{ `$${row.value}` }}</span>
				</template>
        <template #cell(balance)="row">
					<span>{{ `$${row.value}` }}</span>
				</template>
        <template #cell(available)="row">
					<span>{{ `$${row.value}` }}</span>
				</template>
        <template #cell(withdraw)="row">
					<span>{{ `$${row.value}` }}</span>
				</template>
				<template #cell(action)="row">
					<b-button class="text-center" :variant="row.value == 0 ? 'secondary' : 'success'" :disabled="row.value == 0" @click="onApprovePayout(row.index)">Approve</b-button>
				</template>
			</b-table>
    </div>
    <div v-else>
      <b-table
				id="admin-payout-advertiser-table"
				hover
				bordered
				sticky-header
				head-variant="light"
				:fields="advertiserFields"
				:items="advertiserItems"
				class="w-100 text-center"
			>
				<template #cell(index)="row">
					<span>{{ row.index + 1 }}</span>
				</template>
        <template #cell(paid)="row">
					<span>{{ `$${row.value}` }}</span>
				</template>
        <template #cell(pending)="row">
					<span>{{ `$${row.value}` }}</span>
				</template>
        <template #cell(balance)="row">
					<span>{{ `$${row.value}` }}</span>
				</template>
        <template #cell(action)="row">
					<b-button class="text-center" variant="success" @click="showModal(row.index)">Top Up</b-button>
				</template>
			</b-table>
    </div>
    <b-modal
      id="admin-topup-modal"
      hide-footer
      title="Advertiser Topup"
      centered
      ref="adminTopUpModal"
      size="md">
      <b-form-input
        type="text"
        v-model="amountTopup"
        placeholder="Enter amount to top up."
        class="admin-topup-input mt-3 py-3"
        style="font-size: 16px; border-radius: 4px;"
      />
      <div class="d-flex justify-content-end">
        <b-button class="mt-3 px-2 text-center" variant="success" @click="onTopUp">Top Up</b-button>
        <b-button class="mt-3 ml-2 px-2" @click="hideModal">Close</b-button>
      </div>
    </b-modal>
  </div>
</template>

<style lang="scss" moduled>
  .admin-payout {
    padding: 30px;
    height: 100%;
    background-color: white;
    border-radius: 30px;
  }
  .b-table-sticky-header {
    min-height: 100%;
  }
  .admin-topup-input {
    font-size: 20px !important;
    border-radius: 16px;
    height: auto !important;
  }
</style>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
export default {
  name: 'payout',
  data() {
    return {
      mode: 'publisher',
      publisherItems: [],
      advertiserItems: [],
      publisherFields: [
        { key: 'index', label: '#', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe" } },
        { key: 'name', label: 'Username', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'email', label: 'Payoneer Email', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'earned', label: 'Commissions Earned', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'paid', label: 'Commissions Paid', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'balance', label: 'Current Balance', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'available', label: 'Available for Distribution', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'withdraw', label: 'Asked to Withdraw', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'action', label: 'Action', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
      ],
      advertiserFields: [
        { key: 'index', label: '#', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe" } },
        { key: 'name', label: 'Username', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'email', label: 'Email', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'paid', label: 'Paid', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'pending', label: 'Pending', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'balance', label: 'Current Balance', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'action', label: 'Action', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "4%", backgroundColor: "#cff7fe", textAlign: "center" } },
      ],
      amountTopup: 0,
      selectedAdvertiserIndex: 0,
    };
  },
  computed: {
    ...mapGetters(['currentUser'])
  },
  beforeMount() {
    if (!this.currentUser || (this.currentUser && this.currentUser.role != 'admin')) {
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
    this.loadPayoutData();
  },
  methods: {
    async loadPayoutData() {
      const loader = this.$loading.show();
      try {
				const res = await axios.get('/api/admin-payout');
				if (res && res.status == 200) {
          res.data.publisher.map(val => {
            this.publisherItems = [...this.publisherItems, {
              'publisher_id': val.id,
              'name': val.name,
              'email': val.email,
              'earned': val.publisher_vidpopup.reduce((r,v) => r + v.vidgen.cost * v.metrics_click_count, 0),
              'paid': val.publisher_payout.reduce((r,v) => r + (v.status == 'Approved' ? v.withdraw : 0), 0),
              'balance': val.balance,
              'available': val.publisher_vidpopup.reduce((r,v) => r + v.vidgen.cost * v.metrics_pending_count, 0) + (val.publisher_payout.length ? val.publisher_payout[0].remain: 0),
              'withdraw': val.publisher_payout.reduce((r,v) => r + (v.status == 'Pending' ? v.withdraw : 0), 0),
              'pp_ids': val.publisher_payout.map(v => v.id),
              'action': val.publisher_payout.filter(v => v.status == 'Pending'), // 0: Approve button hide, n: show
            }];
          });
          res.data.advertiser.map(val => {
            this.advertiserItems = [...this.advertiserItems, {
              'advertiser_id': val.id,
              'name': val.name,
              'email': val.email,
              'paid': val.advertiser_vidpopup.reduce((r,v) => r + v.vidgen.cost * v.metrics_paid_count, 0),
              'pending': val.advertiser_vidpopup.reduce((r,v) => r + v.vidgen.cost * v.metrics_pending_count, 0),
              'balance': val.balance,
              'action': ''
            }];
          });
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
      loader && loader.hide();
    },
    handleMode(mode) {
      this.mode = mode;
    },
    onApprovePayout(index) {
      let publisher_id = this.publisherItems[index].publisher_id;
      let pp_ids = this.publisherItems[index].pp_ids;
      let withdraw = this.publisherItems[index].withdraw;
      axios.post('/api/admin-publisher-withdraw', {
        publisher_id: publisher_id,
        pp_ids: pp_ids.join(';'),
        withdraw: withdraw,
      })
      .then(res => {
        if (res && res.status == 200) {
          this.$func.makeToast(this, 'Success', 'Approved withdraw successfully!', 'success');
          Object.assign(this.publisherItems[index], {
            'publisher_id': res.data[0].id,
            'name': res.data[0].name,
            'email': res.data[0].email,
            'earned': res.data[0].publisher_vidpopup.reduce((r,v) => r + v.vidgen.cost * v.metrics_click_count, 0),
            'paid': res.data[0].publisher_payout.reduce((r,v) => r + (v.status == 'Approved' ? v.withdraw : 0), 0),
            'balance': res.data[0].balance,
            'available': res.data[0].publisher_vidpopup.reduce((r,v) => r + v.vidgen.cost * v.metrics_pending_count, 0) + (res.data[0].publisher_payout.length ? res.data[0].publisher_payout[0].remain: 0),
            'withdraw': res.data[0].publisher_payout.reduce((r,v) => r + (v.status == 'Pending' ? v.withdraw : 0), 0),
            'pp_ids': res.data[0].publisher_payout.map(v => v.id),
            'action': res.data[0].publisher_payout.filter(v => v.status == 'Pending').length, // 0: Approve button disable, n: not
          });
        }
      })
      .catch(err => {
        console.log('err =>', err);
        this.$func.makeToast(
          this,
          'Error',
          'Cannot withdraw from server!',
          'danger'
        );
      })
    },
    showModal(index) {
      this.selectedAdvertiserIndex = index;
      this.amountTopup = 0;
      this.$bvModal.show('admin-topup-modal');
    },
    hideModal() {
      this.$bvModal.hide('admin-topup-modal');
    },
    onTopUp() {
      let regex = /^(?!0)([0-9]*[.])?[0-9]+$/;
			if (!regex.test(this.amountTopup)) {
				this.$func.makeToast(
          this,
          'Warning',
          'Insert the amount to top up correctly!',
          'warning'
        );
				return;
			}
      let advertiser_id = this.advertiserItems[this.selectedAdvertiserIndex].advertiser_id;
      axios.post('/api/admin-advertiser-topup', {
        advertiser_id: advertiser_id,
        amount: this.amountTopup
      })
      .then(res => {
        if (res && res.status == 200) {
          this.advertiserItems[this.selectedAdvertiserIndex].balance = res.data.balance;
          this.$bvModal.hide('admin-topup-modal');
          this.$func.makeToast(this, 'Success', 'Account top up successfully!', 'success');
        }
      })
      .catch(err => {
        console.log('err =>', err);
        this.$func.makeToast(
          this,
          'Error',
          'Cannot save data to server!',
          'danger'
        );
      })
    }
  }
};
</script>
