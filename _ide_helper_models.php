<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int|null $campagnes_id
 * @property string $content
 * @property string|null $target_region
 * @property string|null $target_interests
 * @property numeric|null $cost_reduction
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advertisements newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advertisements newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advertisements query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advertisements whereCampagnesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advertisements whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advertisements whereCostReduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advertisements whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advertisements whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advertisements whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advertisements whereTargetInterests($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advertisements whereTargetRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Advertisements whereUpdatedAt($value)
 */
	class Advertisements extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $client_id
 * @property string $type
 * @property string $title
 * @property string|null $body
 * @property string|null $action
 * @property string|null $resource_type
 * @property int|null $resource_id
 * @property string|null $link
 * @property bool $read
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clients|null $client
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification unread()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereResourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppNotification whereUserId($value)
 */
	class AppNotification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int $months
 * @property numeric $discount_percent
 * @property numeric $sms_bonus_percent
 * @property bool $priority_support
 * @property bool $advanced_reports
 * @property bool $premium_features
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle whereAdvancedReports($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle whereDiscountPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle whereMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle wherePremiumFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle wherePrioritySupport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle whereSmsBonusPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingCycle whereUpdatedAt($value)
 */
	class BillingCycle extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property string $brand_name
 * @property string|null $logo
 * @property string|null $primary_color
 * @property string|null $secondary_color
 * @property string|null $accent_color
 * @property string|null $font_family
 * @property string|null $description
 * @property string|null $status_motif
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property int|null $approved_by
 * @property int $is_active
 * @property int $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\Clients $client
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereAccentColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereBrandName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereFontFamily($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereSecondaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereStatusMotif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Branding whereUpdatedAt($value)
 */
	class Branding extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $client_id
 * @property string $name
 * @property string $status
 * @property bool $archived
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string|null $region
 * @property string $channel
 * @property int|null $template_id
 * @property array<array-key, mixed>|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Clients|null $client
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Messages> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Templates|null $template
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campagnes withoutTrashed()
 */
	class Campagnes extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Templates> $templates
 * @property-read int|null $templates_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categorie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categorie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categorie query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categorie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categorie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categorie whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categorie whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categorie whereUpdatedAt($value)
 */
	class Categorie extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $channel
 * @property int $spam_rule_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SpamRules $spamRule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel whereSpamRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Channel whereUpdatedAt($value)
 */
	class Channel extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $plan_id
 * @property string $company_name
 * @property string $contact_name
 * @property string $email
 * @property string $phone
 * @property string|null $website
 * @property string|null $forme_juridique
 * @property string|null $numero_immatriculation
 * @property string $city
 * @property string $country
 * @property string $industry
 * @property string $status
 * @property \Illuminate\Support\Carbon $joined_at
 * @property \Illuminate\Support\Carbon|null $last_activity
 * @property \Illuminate\Support\Carbon|null $contract_end
 * @property numeric $monthly_revenue
 * @property numeric $total_spent
 * @property int $messages_sent
 * @property numeric $satisfaction
 * @property int $support_tickets
 * @property array<array-key, mixed>|null $features
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SpamViolation> $SpamViolation
 * @property-read int|null $spam_violation_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Branding> $branding
 * @property-read int|null $branding_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeveloperApiKey> $developerApiKeys
 * @property-read int|null $developer_api_keys_count
 * @property-read string $formatted_monthly_revenue
 * @property-read string $formatted_total_spent
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NotificationSetting> $notificationSettings
 * @property-read int|null $notification_settings_count
 * @property-read \App\Models\Plan|null $plan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereContactName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereContractEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereFormeJuridique($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereLastActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereMessagesSent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereMonthlyRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereNumeroImmatriculation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereSatisfaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereSupportTickets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereTotalSpent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clients whereWebsite($value)
 */
	class Clients extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $industry
 * @property int $owner_client_id
 * @property string|null $logo
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Clients> $branchClients
 * @property-read int|null $branch_clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CompanyGroupBranch> $branches
 * @property-read int|null $branches_count
 * @property-read \App\Models\Clients|null $ownerClient
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroup whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroup whereIndustry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroup whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroup whereOwnerClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroup whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroup whereUpdatedAt($value)
 */
	class CompanyGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $group_id
 * @property int $client_id
 * @property string $zone_name
 * @property string $zone_type
 * @property int $sms_quota_allocated
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clients|null $client
 * @property-read \App\Models\CompanyGroup $group
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroupBranch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroupBranch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroupBranch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroupBranch whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroupBranch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroupBranch whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroupBranch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroupBranch whereSmsQuotaAllocated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroupBranch whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroupBranch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroupBranch whereZoneName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CompanyGroupBranch whereZoneType($value)
 */
	class CompanyGroupBranch extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $regulation
 * @property string $status
 * @property string $last_check
 * @property int $score
 * @property array<array-key, mixed>|null $issues
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplianceChecks newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplianceChecks newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplianceChecks query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplianceChecks whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplianceChecks whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplianceChecks whereIssues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplianceChecks whereLastCheck($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplianceChecks whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplianceChecks whereRegulation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplianceChecks whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplianceChecks whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ComplianceChecks whereUpdatedAt($value)
 */
	class ComplianceChecks extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $campagnes_id
 * @property int $user_id
 * @property string $conflict_type
 * @property string $description
 * @property string $status
 * @property string|null $resolution_suggestion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conflicts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conflicts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conflicts query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conflicts whereCampagnesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conflicts whereConflictType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conflicts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conflicts whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conflicts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conflicts whereResolutionSuggestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conflicts whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conflicts whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Conflicts whereUserId($value)
 */
	class Conflicts extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contact_id
 * @property int $engagement_score
 * @property string $risk_level
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contacts|null $contact
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactScore newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactScore newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactScore query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactScore whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactScore whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactScore whereEngagementScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactScore whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactScore whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactScore whereRiskLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactScore whereUpdatedAt($value)
 */
	class ContactScore extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $client_id
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $region
 * @property string|null $timezone
 * @property int $engagement_score
 * @property string $preferred_channel
 * @property int $is_spammer
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Clients|null $client
 * @property-read \App\Models\DeliveryPreference|null $deliveryPreference
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Events> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Groupe> $groupes
 * @property-read int|null $groupes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Groupe> $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Messages> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Responses> $responses
 * @property-read int|null $responses_count
 * @property-read \App\Models\ContactScore|null $score
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereEngagementScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereIsSpammer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts wherePreferredChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contacts withoutTrashed()
 */
	class Contacts extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contact_id
 * @property array<array-key, mixed>|null $preferred_days
 * @property array<array-key, mixed>|null $preferred_hours
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contacts|null $contact
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryPreference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryPreference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryPreference query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryPreference whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryPreference whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryPreference whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryPreference wherePreferredDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryPreference wherePreferredHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeliveryPreference whereUpdatedAt($value)
 */
	class DeliveryPreference extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property string $name
 * @property string $service_id
 * @property string $secret_token
 * @property string|null $webhook_url
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clients $client
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey whereSecretToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeveloperApiKey whereWebhookUrl($value)
 */
	class DeveloperApiKey extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $client_id
 * @property string $token
 * @property string $platform
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DeviceToken whereUserId($value)
 */
	class DeviceToken extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $region
 * @property string|null $event_date
 * @property string $category
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Events newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Events newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Events query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Events whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Events whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Events whereEventDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Events whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Events whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Events whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Events whereUpdatedAt($value)
 */
	class Events extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $subscription_plan_id
 * @property int $min_quantity
 * @property int|null $max_quantity
 * @property int $price_per_sms
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SubscriptionPlan $plan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraSmsPricing forQuantity(int $quantity)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraSmsPricing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraSmsPricing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraSmsPricing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraSmsPricing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraSmsPricing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraSmsPricing whereMaxQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraSmsPricing whereMinQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraSmsPricing wherePricePerSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraSmsPricing whereSubscriptionPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExtraSmsPricing whereUpdatedAt($value)
 */
	class ExtraSmsPricing extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $frequency_limit
 * @property string $frequency_period
 * @property int $spam_rule_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SpamRules $rule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Frequence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Frequence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Frequence query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Frequence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Frequence whereFrequencyLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Frequence whereFrequencyPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Frequence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Frequence whereSpamRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Frequence whereUpdatedAt($value)
 */
	class Frequence extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $client_id
 * @property int|null $created_by
 * @property string $name
 * @property string $type
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SegmentAudit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contacts> $contacts
 * @property-read int|null $contacts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SegmentRule> $rules
 * @property-read int|null $rules_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Groupe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Groupe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Groupe query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Groupe whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Groupe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Groupe whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Groupe whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Groupe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Groupe whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Groupe whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Groupe whereUpdatedAt($value)
 */
	class Groupe extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $groupe_id
 * @property int $contact_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contacts|null $contact
 * @property-read \App\Models\Groupe $groupe
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupeContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupeContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupeContact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupeContact whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupeContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupeContact whereGroupeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupeContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GroupeContact whereUpdatedAt($value)
 */
	class GroupeContact extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $campagnes_id
 * @property string $metric_name
 * @property numeric $metric_value
 * @property string $recorded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $formatted_data
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Insights newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Insights newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Insights query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Insights whereCampagnesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Insights whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Insights whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Insights whereMetricName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Insights whereMetricValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Insights whereRecordedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Insights whereUpdatedAt($value)
 */
	class Insights extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $service_type
 * @property string $api_key
 * @property string|null $config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integrations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integrations newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integrations query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integrations whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integrations whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integrations whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integrations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integrations whereServiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integrations whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integrations whereUserId($value)
 */
	class Integrations extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Templates> $templates
 * @property-read int|null $templates_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Languages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Languages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Languages query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Languages whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Languages whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Languages whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Languages whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Languages whereUpdatedAt($value)
 */
	class Languages extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $client_id
 * @property string $action
 * @property string|null $resource_type
 * @property int|null $resource_id
 * @property string|null $ip_address
 * @property array<array-key, mixed>|null $details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clients|null $client
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereResourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Logs whereUserId($value)
 */
	class Logs extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $service_type
 * @property string|null $contact_email
 * @property string|null $country
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplacePartners newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplacePartners newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplacePartners query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplacePartners whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplacePartners whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplacePartners whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplacePartners whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplacePartners whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplacePartners whereServiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplacePartners whereUpdatedAt($value)
 */
	class MarketplacePartners extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $partner_id
 * @property string $name
 * @property string|null $description
 * @property numeric|null $price
 * @property string $type
 * @property string|null $sector
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceProducts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceProducts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceProducts query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceProducts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceProducts whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceProducts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceProducts whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceProducts wherePartnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceProducts wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceProducts whereSector($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceProducts whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceProducts whereUpdatedAt($value)
 */
	class MarketplaceProducts extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property int $user_id
 * @property int $rating
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceReviews newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceReviews newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceReviews query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceReviews whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceReviews whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceReviews whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceReviews whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceReviews whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceReviews whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MarketplaceReviews whereUserId($value)
 */
	class MarketplaceReviews extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $campagnes_id
 * @property int $contact_id
 * @property string $content
 * @property string|null $subject
 * @property string|null $sent_at
 * @property string|null $media
 * @property string|null $cta
 * @property string|null $cta_url
 * @property string|null $reply_token
 * @property string $status
 * @property string $channel
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Campagnes|null $campaign
 * @property-read \App\Models\Contacts|null $contact
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Responses> $responses
 * @property-read int|null $responses_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereCampagnesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereCta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereCtaUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereMedia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereReplyToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Messages withoutTrashed()
 */
	class Messages extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $from_user_id
 * @property string $type
 * @property string|null $title
 * @property string $message
 * @property int|null $campaign_id
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Campagnes|null $campaign
 * @property-read \App\Models\User|null $fromUser
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification read()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification unread()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereFromUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUserId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property string $name
 * @property string|null $description
 * @property int $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clients $client
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereUpdatedAt($value)
 */
	class NotificationSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $country
 * @property numeric|null $cost_per_sms
 * @property numeric|null $reliability_score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operators newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operators newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operators query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operators whereCostPerSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operators whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operators whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operators whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operators whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operators whereReliabilityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Operators whereUpdatedAt($value)
 */
	class Operators extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $code
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OtpCode whereUserId($value)
 */
	class OtpCode extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $spam_rule_id
 * @property string $pattern
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SpamRules $rule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patterns newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patterns newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patterns query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patterns whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patterns whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patterns wherePattern($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patterns whereSpamRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Patterns whereUpdatedAt($value)
 */
	class Patterns extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $reference
 * @property int|null $subscription_id
 * @property int $client_id
 * @property int $user_id
 * @property string $payment_method
 * @property string $provider_name
 * @property string|null $provider_transaction_id
 * @property numeric $amount
 * @property string $currency
 * @property string $status
 * @property string|null $phone
 * @property string|null $payment_url
 * @property array<array-key, mixed>|null $provider_response
 * @property string|null $failure_reason
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clients $client
 * @property-read \App\Models\Subscription|null $subscription
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction wherePaymentUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereProviderName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereProviderResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereProviderTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PaymentTransaction whereUserId($value)
 */
	class PaymentTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property numeric $price
 * @property string $currency
 * @property int $included_sms_volume
 * @property numeric $overuse_price_per_sms
 * @property string|null $limitations
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Clients> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PlanFeature> $features
 * @property-read int|null $features_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SmsPricingTier> $pricingTiers
 * @property-read int|null $pricing_tiers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereIncludedSmsVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereLimitations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereOverusePricePerSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereUpdatedAt($value)
 */
	class Plan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $plan_id
 * @property string $feature
 * @property int $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Plan $plan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature whereFeature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlanFeature whereUpdatedAt($value)
 */
	class PlanFeature extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $points_per_action
 * @property string $reward_type
 * @property numeric|null $reward_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programs query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programs whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programs whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programs wherePointsPerAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programs whereRewardType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programs whereRewardValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programs whereUpdatedAt($value)
 */
	class Programs extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $message_id
 * @property int $contact_id
 * @property string $content
 * @property string $received_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contacts|null $contact
 * @property-read \App\Models\Messages|null $message
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Responses newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Responses newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Responses query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Responses whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Responses whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Responses whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Responses whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Responses whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Responses whereReceivedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Responses whereUpdatedAt($value)
 */
	class Responses extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $role
 * @property int $peut_creer_utilisateur
 * @property int $peut_modifier_utilisateur
 * @property int $peut_supprimer_utilisateur
 * @property int $peut_attribuer_permissions
 * @property int $peut_integrer_outils_externes
 * @property int $peut_gerer_routage_sms
 * @property int $peut_configurer_parametres_globaux
 * @property int $peut_generer_journaux_audit
 * @property int $peut_gerer_budget
 * @property int $peut_definir_alertes_budget
 * @property int $peut_creer_campagne
 * @property int $peut_gerer_campagne
 * @property int $peut_supprimer_campagne
 * @property int $peut_envoyer_campagne
 * @property int $peut_partager_brouillon
 * @property int $peut_approuver_campagne
 * @property int $peut_voir_analytiques
 * @property int $peut_segmenter_audience
 * @property int $peut_personnaliser_contenu
 * @property int $peut_voir_analytiques_regionales
 * @property int $peut_localiser_contenu
 * @property int $peut_voir_campagnes
 * @property int $peut_voir_contacts
 * @property int $peut_gerer_contacts
 * @property int $peut_voir_detail_campagnes
 * @property int $peut_voir_budget
 * @property int $peut_ajouter_branding
 * @property int $peut_recharger_credits
 * @property int $peut_executer_campagne
 * @property int $peut_modifier_details_mineurs_campagne
 * @property int $peut_voir_reponses_clients
 * @property int $peut_recevoir_notifications
 * @property int $peut_acceder_api
 * @property int $peut_voir_analytiques_api
 * @property int $peut_developper_plugins
 * @property int $peut_envoyer_alertes_publiques
 * @property int $peut_voir_tableau_impact
 * @property int $peut_collecter_retours
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutAccederApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutAjouterBranding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutApprouverCampagne($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutAttribuerPermissions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutCollecterRetours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutConfigurerParametresGlobaux($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutCreerCampagne($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutCreerUtilisateur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutDefinirAlertesBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutDevelopperPlugins($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutEnvoyerAlertesPubliques($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutEnvoyerCampagne($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutExecuterCampagne($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutGenererJournauxAudit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutGererBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutGererCampagne($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutGererContacts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutGererRoutageSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutIntegrerOutilsExternes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutLocaliserContenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutModifierDetailsMineursCampagne($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutModifierUtilisateur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutPartagerBrouillon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutPersonnaliserContenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutRecevoirNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutRechargerCredits($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutSegmenterAudience($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutSupprimerCampagne($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutSupprimerUtilisateur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutVoirAnalytiques($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutVoirAnalytiquesApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutVoirAnalytiquesRegionales($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutVoirBudget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutVoirCampagnes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutVoirContacts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutVoirDetailCampagnes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutVoirReponsesClients($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions wherePeutVoirTableauImpact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolesPermissions whereUpdatedAt($value)
 */
	class RolesPermissions extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $groupe_id
 * @property int $user_id
 * @property string $action
 * @property array<array-key, mixed>|null $old_value
 * @property array<array-key, mixed>|null $new_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Groupe $groupe
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentAudit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentAudit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentAudit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentAudit whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentAudit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentAudit whereGroupeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentAudit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentAudit whereNewValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentAudit whereOldValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentAudit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentAudit whereUserId($value)
 */
	class SegmentAudit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $groupe_id
 * @property string $field
 * @property string $operator
 * @property string $value
 * @property string $logical
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Groupe $groupe
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentRule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentRule whereField($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentRule whereGroupeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentRule whereLogical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentRule whereOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentRule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SegmentRule whereValue($value)
 */
	class SegmentRule extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property string $name
 * @property string $status
 * @property bool $is_active
 * @property bool $is_default
 * @property string|null $status_motif
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\Clients $client
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName whereStatusMotif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SenderName whereUpdatedAt($value)
 */
	class SenderName extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $plan_id
 * @property int $min_volume
 * @property int|null $max_volume
 * @property numeric $price_per_sms
 * @property string $currency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Plan $plan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsPricingTier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsPricingTier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsPricingTier query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsPricingTier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsPricingTier whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsPricingTier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsPricingTier whereMaxVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsPricingTier whereMinVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsPricingTier wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsPricingTier wherePricePerSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsPricingTier whereUpdatedAt($value)
 */
	class SmsPricingTier extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property int|null $subscription_id
 * @property int $sms_count
 * @property numeric $price
 * @property string $currency
 * @property string|null $payment_ref
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Clients|null $client
 * @property-read \App\Models\Subscription|null $subscription
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment wherePaymentRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment whereSmsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SmsTopupPayment whereUpdatedAt($value)
 */
	class SmsTopupPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $message_id
 * @property int|null $contact_id
 * @property int $keyword_id
 * @property string $detected_at
 * @property string $action_taken
 * @property string|null $details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamDetections newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamDetections newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamDetections query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamDetections whereActionTaken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamDetections whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamDetections whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamDetections whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamDetections whereDetectedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamDetections whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamDetections whereKeywordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamDetections whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamDetections whereUpdatedAt($value)
 */
	class SpamDetections extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $keyword
 * @property string|null $category
 * @property int $spam_rule_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $formatted_keyword
 * @property-read \App\Models\SpamRules $rule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamKeywords newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamKeywords newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamKeywords query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamKeywords whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamKeywords whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamKeywords whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamKeywords whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamKeywords whereSpamRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamKeywords whereUpdatedAt($value)
 */
	class SpamKeywords extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $type
 * @property string $content
 * @property string $sender
 * @property string $recipient
 * @property string $reason
 * @property string $status
 * @property string $timestamp
 * @property int $risk_score
 * @property int|null $rule_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SpamRules|null $rule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports whereRecipient($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports whereRiskScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports whereRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports whereSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamReports whereUpdatedAt($value)
 */
	class SpamReports extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nom_regle
 * @property string $type
 * @property string|null $condition
 * @property string $action
 * @property string $status
 * @property int|null $matches
 * @property string $severity
 * @property string|null $description
 * @property int $auto_learn
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Channel> $channels
 * @property-read int|null $channels_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\contentRules> $contentRules
 * @property-read int|null $content_rules_count
 * @property-read \App\Models\Frequence|null $frequence
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SpamKeywords> $keywords
 * @property-read int|null $keywords_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Patterns> $patterns
 * @property-read int|null $patterns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\senderDomains> $senderDomains
 * @property-read int|null $sender_domains_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules whereAutoLearn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules whereMatches($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules whereNomRegle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamRules whereUpdatedAt($value)
 */
	class SpamRules extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property int|null $campaign_id
 * @property int $spam_rule_id
 * @property string $channel
 * @property string $action
 * @property string $severity
 * @property string $reason
 * @property int $score
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Campagnes|null $campaign
 * @property-read \App\Models\Clients $client
 * @property-read \App\Models\SpamRules $rule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation blocked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation critical()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation forClient(int $clientId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation whereSpamRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpamViolation whereUpdatedAt($value)
 */
	class SpamViolation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $client_id
 * @property int $subscription_plan_id
 * @property int $billing_cycle_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property \Illuminate\Support\Carbon|null $next_billing_date
 * @property string $status
 * @property bool $auto_renew
 * @property numeric $price
 * @property string $currency
 * @property string|null $payment_ref
 * @property string|null $payment_status
 * @property int $sms_quota
 * @property int $sms_used
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BillingCycle $billingCycle
 * @property-read \App\Models\Clients $client
 * @property-read \App\Models\SubscriptionPlan $plan
 * @property-read \App\Models\SubscriptionPlan $subscriptionplan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription expiringSoon($days = 3)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereAutoRenew($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereBillingCycleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereNextBillingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePaymentRef($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereSmsQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereSmsUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereSubscriptionPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUpdatedAt($value)
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $price_monthly_base
 * @property int $sms_included_monthly
 * @property int $sms_price_reference
 * @property int|null $rollover_months
 * @property array<array-key, mixed>|null $features
 * @property bool $is_freemium
 * @property bool $has_long_term_discounts
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExtraSmsPricing> $extraSmsPricing
 * @property-read int|null $extra_sms_pricing_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscription
 * @property-read int|null $subscription_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan paid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereHasLongTermDiscounts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereIsFreemium($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan wherePriceMonthlyBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereRolloverMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereSmsIncludedMonthly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereSmsPriceReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubscriptionPlan whereUpdatedAt($value)
 */
	class SubscriptionPlan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $contact_id
 * @property string $subject
 * @property string $description
 * @property string $status
 * @property int|null $language_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Plan|null $plan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTickets newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTickets newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTickets query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTickets whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTickets whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTickets whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTickets whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTickets whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTickets whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTickets whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTickets whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTickets whereUserId($value)
 */
	class SupportTickets extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Templates> $templates
 * @property-read int|null $templates_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $template_id
 * @property int $tag_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Tag $tag
 * @property-read \App\Models\Templates $template
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagTemplate whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagTemplate whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TagTemplate whereUpdatedAt($value)
 */
	class TagTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $extrait
 * @property string $channel
 * @property string $content
 * @property int $is_favori
 * @property string|null $branding_logo
 * @property string|null $branding_colors
 * @property string|null $sector
 * @property int|null $language_id
 * @property int $category_id
 * @property int|null $client_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Campagnes> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \App\Models\Categorie $category
 * @property-read \App\Models\Clients|null $client
 * @property-read \App\Models\Languages|null $language
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereBrandingColors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereBrandingLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereExtrait($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereIsFavori($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereSector($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Templates whereUpdatedAt($value)
 */
	class Templates extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $type
 * @property string|null $content_url
 * @property int|null $language_id
 * @property string|null $sector
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingResources newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingResources newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingResources query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingResources whereContentUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingResources whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingResources whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingResources whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingResources whereSector($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingResources whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingResources whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingResources whereUpdatedAt($value)
 */
	class TrainingResources extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $contact_id
 * @property numeric $amount
 * @property string $provider
 * @property string $transaction_date
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contacts|null $contact
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transactions whereUpdatedAt($value)
 */
	class Transactions extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property bool $two_factor_enabled
 * @property string|null $google_id
 * @property string|null $facebook_id
 * @property string|null $github_id
 * @property string|null $gitlab_id
 * @property string|null $bitbucket_id
 * @property string|null $slack_id
 * @property string|null $twitch_id
 * @property string|null $twitter_openid_id
 * @property string|null $linkedin_openid_id
 * @property string|null $profil
 * @property string $role
 * @property int|null $client_id
 * @property string|null $activation_token
 * @property string|null $phone
 * @property string|null $bio
 * @property string $status
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Campagnes> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \App\Models\Clients|null $client
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\RolesPermissions|null $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserSession> $sessions
 * @property-read int|null $sessions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereActivationToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBitbucketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFacebookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGithubId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGitlabId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLinkedinOpenidId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSlackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwitchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwitterOpenidId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $device_id
 * @property string|null $device_name
 * @property string|null $browser
 * @property string|null $os
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property bool $is_current
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereDeviceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereIsCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereOs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUserId($value)
 */
	class UserSession extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $spam_rule_id
 * @property \App\Models\SpamRules $rule
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|contentRules newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|contentRules newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|contentRules query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|contentRules whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|contentRules whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|contentRules whereRule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|contentRules whereSpamRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|contentRules whereUpdatedAt($value)
 */
	class contentRules extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $spam_rule_id
 * @property string $domain
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SpamRules $rule
 * @method static \Illuminate\Database\Eloquent\Builder<static>|senderDomains newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|senderDomains newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|senderDomains query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|senderDomains whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|senderDomains whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|senderDomains whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|senderDomains whereSpamRuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|senderDomains whereUpdatedAt($value)
 */
	class senderDomains extends \Eloquent {}
}

