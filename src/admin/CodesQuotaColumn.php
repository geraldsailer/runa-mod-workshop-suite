<?php

declare(strict_types=1);

namespace Runa\WorkshopSuite\Admin;

/**
 * Ensures the Pods manage table lists the quota field.
 */
final class CodesQuotaColumn {
	private const QUOTA_FIELD = 'rna_wss_codes_quota';
	private const WORKSHOP_FIELD = 'rna_wss_ct_rel_course';

	public function register(): void {
		add_filter('pods_admin_ui_fields_rna_wss_code', [$this, 'addColumns'], 10, 3);
	}

	/**
	 * @param array<string, array<string, mixed>> $fields
	 * @param array<string, mixed>|null           $pod
	 * @param mixed                               $pods
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function addColumns(array $fields, $pod = null, $pods = null): array {
		$quotaField = $this->resolveQuotaField($pod);
		$workshopField = $this->resolveWorkshopField($pod);

		unset($fields[self::QUOTA_FIELD], $fields[self::WORKSHOP_FIELD]);

		$fields = $this->insertAfterNameColumn($fields, $quotaField);
		$fields = $this->insertAfterField($fields, self::QUOTA_FIELD, self::WORKSHOP_FIELD, $workshopField);

		return $fields;
	}

	/**
	 * @param array<string, mixed>|null $pod
	 *
	 * @return array<string, mixed>
	 */
	private function resolveQuotaField($pod): array {
		return $this->resolveFieldDefinition(
			$pod,
			self::QUOTA_FIELD,
			[
				'name'  => self::QUOTA_FIELD,
				'label' => __('Kontingent', 'runa-mod-workshop-suite'),
				'type'  => 'number',
				'width' => '10%',
			]
		);
	}

	/**
	 * @param array<string, mixed>|null $pod
	 */
	private function resolveWorkshopField($pod): array {
		return $this->resolveFieldDefinition(
			$pod,
			self::WORKSHOP_FIELD,
			[
				'name'  => self::WORKSHOP_FIELD,
				'label' => __('Workshop', 'runa-mod-workshop-suite'),
				'type'  => 'pick',
				'width' => '20%',
			]
		);
	}

	/**
	 * @param array<string, mixed>|null $pod
	 * @param array<string, mixed>      $fallback
	 *
	 * @return array<string, mixed>
	 */
	private function resolveFieldDefinition($pod, string $fieldName, array $fallback): array {
		if (is_array($pod)) {
			if (isset($pod['fields'][$fieldName]) && is_array($pod['fields'][$fieldName])) {
				return $pod['fields'][$fieldName];
			}

			if (isset($pod['object_fields'][$fieldName]) && is_array($pod['object_fields'][$fieldName])) {
				return $pod['object_fields'][$fieldName];
			}
		}

		if (class_exists('\PodsForm')) {
			/** @var array<string, mixed> $fallback */
			$fallback = \PodsForm::field_setup($fallback, null, $fallback['type']);
		}

		return $fallback;
	}

	/**
	 * @param array<string, array<string, mixed>> $fields
	 * @param array<string, mixed>                $quotaField
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function insertAfterNameColumn(array $fields, array $quotaField): array {
		$ordered = [];
		$inserted = false;

		foreach ($fields as $key => $field) {
			$ordered[$key] = $field;
			$fieldData = $this->normalizeField($field);

			if (! $inserted && $this->isNameColumn($key, $fieldData)) {
				$ordered[self::QUOTA_FIELD] = $quotaField;
				$inserted = true;
			}
		}

		if (! $inserted) {
			$ordered[self::QUOTA_FIELD] = $quotaField;
		}

		return $ordered;
	}

	/**
	 * @param array<string, array<string, mixed>> $fields
	 * @param array<string, mixed>                $newField
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function insertAfterField(array $fields, string $afterKey, string $newKey, array $newField): array {
		$ordered = [];
		$inserted = false;

		foreach ($fields as $key => $field) {
			$ordered[$key] = $field;

			if (! $inserted && $key === $afterKey) {
				$ordered[$newKey] = $newField;
				$inserted = true;
			}
		}

		if (! $inserted) {
			$ordered[$newKey] = $newField;
		}

		return $ordered;
	}

	/**
	 * @param string               $key
	 * @param array<string, mixed> $field
	 */
	private function isNameColumn(string $key, array $field): bool {
		$canonical = ['post_title', 'name', 'title'];

		if (in_array($key, $canonical, true)) {
			return true;
		}

		$label = strtolower((string) ($field['label'] ?? ''));

		return str_contains($label, 'name') || str_contains($label, 'titel');
	}

	/**
	 * @param array<string, mixed>|object $field
	 *
	 * @return array<string, mixed>
	 */
	private function normalizeField($field): array {
		if (is_array($field)) {
			return $field;
		}

		if (is_object($field) && method_exists($field, '__toString')) {
			return ['label' => (string) $field];
		}

		if (is_object($field)) {
			return array_filter(
				(array) $field,
				static fn ($value, $key) => ! is_numeric($key),
				ARRAY_FILTER_USE_BOTH
			);
		}

		return [];
	}
}
