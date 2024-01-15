<template>
  <div class="admin-pay-publisher">
    <b-form-select
      v-model="selectedYear"
      :options="yearList"
      class="ml-3"
      @change="updateYear"
    />
    <b-form-select
      v-model="selectedMonth"
      :options="monthList"
      class="ml-3"
      @change="updateMonth"
    />
    <div>
      <b-table
				id="admin-pay-publisher-table"
				hover
				bordered
				sticky-header
				head-variant="light"
				:fields="fields"
				:items="items"
				class="w-100 text-center mt-4"
			>
				<template #cell(index)="row">
					<span>{{ row.index + 1 }}</span>
				</template>
        <template #cell(pending)="row">
					<span>{{ `$${row.value}` }}</span>
				</template>
        <template #cell(balance)="row">
					<span>{{ `$${row.value}` }}</span>
				</template>
        <template #cell(action)="row">
					<b-button class="text-center px-3" :variant="row.value ? 'success' : 'secondary'" @click="onPayPublisher(row.index)" :disabled="row.value == 0">Pay</b-button>
				</template>
			</b-table>
    </div>
    <div v-if="lackBalance && lackBalanceWarning.length" class="mt-5">
      <h5>{{ lackBalanceWarning[0].pname }} ({{ lackBalanceWarning[0].pemail }})</h5>
      <h6 class="pl-3" v-for="(val, index) in lackBalanceWarning" :key="index">
        Balance of {{ val.name }} ({{ val.email }}) is less than ${{ val.pay }}
      </h6>
    </div>
  </div>
</template>

<style lang="scss" moduled>
$light-color: #f0f1f3;
  .admin-pay-publisher {
    padding: 30px;
    height: 100%;
    background-color: white;
    border-radius: 30px;
  }
  .b-table-sticky-header {
    min-height: 100%;
  }
  .custom-select {
    width: 160px;
    background-color: $light-color;
    border: 1px solid $light-color;
    padding: 5px 30px;
    border-radius: 30px;
    font-weight: 700;
    color: black;
    cursor: pointer;
    option {
      padding: 5px 30px;
    }
  }
  .btn-refresh {
    border-radius: 30px;
  }
</style>

<script>
import axios from 'axios';
export default {
  name: 'pay',
  data() {
    return {
      yearList: [
        { value: 2023, text: '2023'},
        { value: 2024, text: '2024'},
        { value: 2025, text: '2025'},
        { value: 2026, text: '2026'},
        { value: 2027, text: '2027'},
        { value: 2028, text: '2028'},
        { value: 2029, text: '2029'},
        { value: 2030, text: '2030'},
      ],
      monthList: [
        { value: 1, text: 'Jan'},
        { value: 2, text: 'Feb'},
        { value: 3, text: 'Mar'},
        { value: 4, text: 'Apr'},
        { value: 5, text: 'May'},
        { value: 6, text: 'Jun'},
        { value: 7, text: 'Jul'},
        { value: 8, text: 'Aug'},
        { value: 9, text: 'Sep'},
        { value: 10, text: 'Oct'},
        { value: 11, text: 'Nov'},
        { value: 12, text: 'Dec'},
      ],
      selectedYear: 0,
      selectedMonth: 0,
      items: [],
      fields: [
        { key: 'index', label: '#', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "8%", backgroundColor: "#cff7fe" } },
        { key: 'name', label: 'Username', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'email', label: 'Payoneer Email', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'pending', label: 'Pending', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "10%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'balance', label: 'Current Balance', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "10%", backgroundColor: "#cff7fe", textAlign: "center" } },
        { key: 'action', label: 'Action', tdClass: 'align-middle text-center', thClass: 'align-middle', thStyle: { width: "1-%", backgroundColor: "#cff7fe", textAlign: "center" } },
      ],
      lackBalance: false,
      lackBalanceWarning: [],
    };
  },
  beforeMount() {
    const date = new Date();
    this.selectedYear = date.getFullYear();
    this.selectedMonth = date.getMonth() + 1;
  },
  mounted() {
    this.loadPayData();
  },
  methods: {
    async updateYear() {
      this.selectedMonth = 1;
      this.loadPayData();
    },
    async updateMonth() {
      this.loadPayData();
    },
    async loadPayData() {
      const loader = this.$loading.show();
      let lastDate = new Date(this.selectedYear, this.selectedMonth, 0);
      let lastday = lastDate.getDate();
      try {
        const res = await axios.get('/api/admin-pay-publisher?year='+this.selectedYear+'&month='+this.selectedMonth+'&lastday='+lastday)
        if (res && res.status == 200) {
          this.items = [];
          res.data.map(val => {
            let metrics = [];
            val.publisher_vidpopup.map(pv => {
              let tmp = metrics.filter(m => m.creator_id == pv.creator_id);
              if (tmp.length) {
                tmp[0].pay += pv.vidgen.cost * pv.metrics_click_count;
              } else {
                metrics = [...metrics, {
                  creator_id: pv.creator_id,
                  publisher_id: pv.publisher_id,
                  name: pv.advertiser.name,
                  email: pv.advertiser.email,
                  pname: val.name,
                  pemail: val.email,
                  balance: pv.advertiser.balance,
                  pay: pv.vidgen.cost * pv.metrics_click_count,
                }];
              }
            });
            this.items = [...this.items, {
              'publisher_id': val.id,
              'name': val.name,
              'email': val.email,
              'pending': val.publisher_vidpopup.reduce((r,v) => r + v.vidgen.cost * v.metrics_click_count, 0),
              'balance': val.balance,
              'action': val.publisher_vidpopup.reduce((r,v) => r + v.vidgen.cost * v.metrics_click_count, 0),
              'metrics': metrics,
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
    onRefresh() {
      this.items = [];
      this.loadPayData();
    },
    onPayPublisher(index) {
      this.$bvModal.msgBoxConfirm('Are you sure want to pay?')
			.then(val => {
				if (val) {
          this.lackBalance = false;
          this.lackBalanceWarning = [];
          this.items[index].metrics.map(val => {
            if (val.balance < val.pay) {
              this.lackBalance = true;
              this.lackBalanceWarning = [...this.lackBalanceWarning, {
                'name': val.name,
                'email': val.email,
                'pay': val.pay,
                'pname': val.pname,
                'pemail': val.pemail
              }]
            }
          });
          if (this.lackBalance) {
            this.$func.makeToast(
              this,
              'Error',
              'Cannot pay as lack of balance!',
              'danger'
            );
            return;
          }
          let lastDate = new Date(this.selectedYear, this.selectedMonth, 0);
          let lastday = lastDate.getDate();
          axios.post('/api/admin-pay-publisher', {
            publisher_id: this.items[index].publisher_id,
            year: this.selectedYear,
            month: this.selectedMonth,
            lastday: lastday
          })
          .then(res => {
            if (res && res.status == 200) {
              console.log('res=>', res);
              let metrics = [];
              res.data[0].publisher_vidpopup.map(pv => {
                let tmp = metrics.filter(m => m.creator_id == pv.creator_id);
                if (tmp.length) {
                  tmp[0].pay += pv.vidgen.cost * pv.metrics_click_count;
                } else {
                  metrics = [...metrics, {
                    creator_id: pv.creator_id,
                    publisher_id: pv.publisher_id,
                    name: pv.advertiser.name,
                    email: pv.advertiser.email,
                    pname: res.data[0].name,
                    pemail: res.data[0].email,
                    balance: pv.advertiser.balance,
                    pay: pv.vidgen.cost * pv.metrics_click_count,
                  }];
                }
              });
              Object.assign(this.items[index], {
                'publisher_id': res.data[0].id,
                'name': res.data[0].name,
                'email': res.data[0].email,
                'pending': res.data[0].publisher_vidpopup.reduce((r,v) => r + v.vidgen.cost * v.metrics_click_count, 0),
                'balance': res.data[0].balance,
                'action': res.data[0].publisher_vidpopup.reduce((r,v) => r + v.vidgen.cost * v.metrics_click_count, 0),
                'metrics': metrics,
              });
              this.$func.makeToast(this, 'Success', 'Paid successfully!', 'success');
            }
          })
          .catch(err => {
            console.log('err =>', err);
            this.$func.makeToast(
              this,
              'Error',
              'Cannot pay!',
              'danger'
            );
          })
        }
			})
			.catch(err => {
			})
    }
  }
};
</script>