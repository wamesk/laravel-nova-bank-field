<template>
  <DefaultField
    :field="field"
    :errors="errors"
    :show-help-text="showHelpText"
    :full-width-content="fullWidthContent"
  >
    <template #field>
      <div class="input-wrapper">
        <input
            :id="field.attribute + '-name'"
            type="text"
            class="w-full form-control form-input form-input-bordered"
            :class="errorClasses"
            :placeholder="__('bank.placeholder')"
            v-model="formData.name"
            required="true"
        />
      </div>

      <div class="input-wrapper">
        <input
          :id="field.attribute + '-iban'"
          type="text"
          class="w-full form-control form-input form-input-bordered"
          :class="errorClasses"
          :placeholder="__('iban.placeholder')"
          v-model="formData.iban"
          @change="changeIban"
          required="true"
        />

        <div v-if="status.iban === true" class="status success text-green-500">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" class="inline-block component-heroicons-outline-check-circle component-icon component-icon-boolean" role="presentation"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          {{ __('iban.valid') }}
        </div>

        <div v-if="status.iban === false" class="status error text-red-500">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" class="inline-block component-heroicons-outline-x-circle component-icon component-icon-boolean" role="presentation"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
          {{ __('iban.invalid') }}
        </div>
      </div>

      <div class="row">
        <div class="input-wrapper col" :class="{ 'success': status.bban === true, 'error': status.bban === false }">
          <input
              :id="field.attribute + '-bban'"
              type="text"
              class="w-full form-control form-input form-input-bordered"
              :class="errorClasses"
              :placeholder="__('bban.placeholder')"
              v-model="formData.bban"
              @change="changeBbanBic"
              required="true"
          />
        </div>

        <div class="input-wrapper col" :class="{ 'success': status.bic === true, 'error': status.bic === false }">
          <input
              :id="field.attribute + '-bic'"
              type="text"
              class="w-full form-control form-input form-input-bordered"
              :class="errorClasses"
              :placeholder="__('bic.placeholder')"
              v-model="formData.bic"
              @change="changeBbanBic"
              required="true"
          />
        </div>
      </div>

      <div v-if="status.bban === true && status.bic === true" class="status success text-green-500">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" class="inline-block component-heroicons-outline-check-circle component-icon component-icon-boolean" role="presentation"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ __('bban_and_bic.valid') }}
      </div>

      <div v-if="status.bban === false || status.bic === false" class="status error text-red-500">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" class="inline-block component-heroicons-outline-x-circle component-icon component-icon-boolean" role="presentation"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ __('bban_or_bic.invalid') }}
      </div>
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import { extractIBAN, extractAccountNumberAndBIC } from '../functions'

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field'],

  data() {
    return {
      formData: {
        name: null,
        iban: null,
        bban: null,
        bic: null,
      },
      status: {
        name: null,
        iban: null,
        bban: null,
        bic: null,
      }
    }
  },

  methods: {
    /**
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      let value = this.field.value || ''

      if (value !== '[]') {
        if (typeof value === 'string' || value instanceof String) {
          // FIX for whitecube/nova-flexible-content
          if (value.charAt(0) !== '{') value = '{' + value + '}'

          value = JSON.parse(value)
        }

        this.formData.name = value.name
        this.formData.iban = value.iban
        this.formData.bban = value.bban
        this.formData.bic = value.bic
      }

      this.value = value
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      this.value = JSON.stringify(extractIBAN(this.formData.iban))

      // FIX for whitecube/nova-flexible-content
      let flexibleContent = document.getElementById(this.fieldAttribute.split('__')[0])?.classList.contains('component-form-nova-flexible-content-group')
      if (flexibleContent) this.value = this.value.slice(1, -1)

      formData.append(this.field.attribute, this.value || '')
    },

    changeIban() {
      this.resetStatus()

      this.formData.bban = null
      this.formData.bic = null

      const data = extractIBAN(this.formData.iban)

      if (data.valid === false) {
        this.status.iban = false
      } else {
        this.status.iban = true
        this.formData.name = data.name
        this.formData.bban = data.bban
        this.formData.bic = data.bic
      }
    },

    changeBbanBic() {
      this.resetStatus()

      this.formData.iban = null
      if (!this.formData.bban) this.status.bban = false
      if (!this.formData.bic) this.status.bic = false

      if (this.formData.bban && this.formData.bic) {
        const data = extractAccountNumberAndBIC(this.formData.bban, this.formData.bic)

        if (data.valid === false) {
          this.status.bban = false
          this.status.bic = false
        } else {
          this.status.bban = true
          this.status.bic = true
          this.formData.name = data.name
          this.formData.iban = data.iban
        }
      }
    },

    resetStatus() {
      this.status.name = null
      this.status.iban = null
      this.status.bban = null
      this.status.bic = null
    }
  },
}
</script>
