<template>
  <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0 component-panel-item">
    <div class="md:w-1/4 md:py-3">
      <h4 class="font-normal">
        <span>{{ field.name }}</span>
      </h4>
    </div>

    <div class="md:w-3/4 md:py-3 break-all lg:break-words">
      <div v-if="list === true" class="list">
        <div v-for="item in bank" class="item">
          <p><strong>{{ item.name }}</strong></p>
          <p>{{ __('iban.title', {'iban': item.iban}) }}</p>
          <p>
            {{ __('bban.title', {'account_number': item.bban, 'code': item.bank_code}) }}
            {{ __('bic.title', {'bic': item.bic}) }}
          </p>
        </div>
      </div>

      <div v-else-if="typeof bank.iban !== 'undefined'">
        <p><strong>{{ bank.name }}</strong></p>
        <p>{{ __('iban.title', {'iban': bank.iban}) }}</p>
        <p>
          {{ __('bban.title', {'account_number': bank.bban, 'code': bank.bank_code}) }}
          {{ __('bic.title', {'bic': bank.bic}) }}
        </p>
      </div>

      <p v-else>—</p>
    </div>
  </div>
</template>

<script>
export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
  data() {
    return {
        bank: [],
        list: false
    }
  },
  mounted() {
    this.prepareBank()
  },
  methods: {
    prepareBank() {
        const bank = this.field.value

        if (bank !== null) {
            if (typeof bank === 'object') {
                const list = []

                bank.map(function (item) {
                    list.push(JSON.parse(item.fields.bank))
                })

                this.bank = list
                this.list = true
            } else if (bank.startsWith('{')) {
                this.bank = JSON.parse(bank)
            }
        }
    }
  }
}
</script>
<style>
.item {
  margin-top: 10px;
}
.item:first-child {
  margin-top: 0;
}
</style>
