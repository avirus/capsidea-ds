

/****************************** INIT_GLOBALS *********************************/
    currentDate = new Date();
    data = {};
    myObj = {};
    dateFrom = {};
    dateTill = {};
    json_data = [];
    style_display = false;
	ttid = 0;
    tid = 0;
	name_modified = "";
	name = "";
	n = {
    "ga:userType": {
        "id": "ga:userType",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "User",
            "status": "PUBLIC",
            "uiName": "User Type",
            "description": "A boolean indicating if a user is new or returning. Possible values: New Visitor, Returning Visitor.",
            "allowedInSegments": "true"
        }
    },
    "ga:visitorType": {
        "id": "ga:visitorType",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:userType",
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "User",
            "status": "DEPRECATED",
            "uiName": "User Type",
            "description": "A boolean indicating if a user is new or returning. Possible values: New Visitor, Returning Visitor.",
            "allowedInSegments": "true"
        }
    },
    "ga:sessionCount": {
        "id": "ga:sessionCount",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "User",
            "status": "PUBLIC",
            "uiName": "Count of Sessions",
            "description": "The session index for a user to your property. Each session from a unique user will get its own incremental index starting from 1 for the first session. Subsequent sessions do not change previous session indicies. For example, if a certain user has 4 sessions to your website, sessionCount for that user will have 4 distinct values of '1' through '4'.",
            "allowedInSegments": "true"
        }
    },
    "ga:visitCount": {
        "id": "ga:visitCount",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:sessionCount",
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "User",
            "status": "DEPRECATED",
            "uiName": "Count of Sessions",
            "description": "The session index for a user to your property. Each session from a unique user will get its own incremental index starting from 1 for the first session. Subsequent sessions do not change previous session indicies. For example, if a certain user has 4 sessions to your website, sessionCount for that user will have 4 distinct values of '1' through '4'.",
            "allowedInSegments": "true"
        }
    },
    "ga:daysSinceLastSession": {
        "id": "ga:daysSinceLastSession",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "User",
            "status": "PUBLIC",
            "uiName": "Days Since Last Session",
            "description": "The number of days elapsed since users last visited your property. Used to calculate user loyalty.",
            "allowedInSegments": "true"
        }
    },
    "ga:daysSinceLastVisit": {
        "id": "ga:daysSinceLastVisit",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:daysSinceLastSession",
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "User",
            "status": "DEPRECATED",
            "uiName": "Days Since Last Session",
            "description": "The number of days elapsed since users last visited your property. Used to calculate user loyalty.",
            "allowedInSegments": "true"
        }
    },
    "ga:userDefinedValue": {
        "id": "ga:userDefinedValue",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "User",
            "status": "PUBLIC",
            "uiName": "User Defined Value",
            "description": "The value provided when you define custom user segments for your property.",
            "allowedInSegments": "true"
        }
    },
    "ga:users": {
        "id": "ga:users",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "User",
            "status": "PUBLIC",
            "uiName": "Users",
            "description": "Total number of users to your property for the requested time period."
        }
    },
    "ga:visitors": {
        "id": "ga:visitors",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:users",
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "User",
            "status": "DEPRECATED",
            "uiName": "Users",
            "description": "Total number of users to your property for the requested time period."
        }
    },
    "ga:newUsers": {
        "id": "ga:newUsers",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "User",
            "status": "PUBLIC",
            "uiName": "New Users",
            "description": "The number of users whose session on your property was marked as a first-time session.",
            "allowedInSegments": "true"
        }
    },
    "ga:newVisits": {
        "id": "ga:newVisits",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:newUsers",
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "User",
            "status": "DEPRECATED",
            "uiName": "New Users",
            "description": "The number of users whose session on your property was marked as a first-time session.",
            "allowedInSegments": "true"
        }
    },
    "ga:percentNewSessions": {
        "id": "ga:percentNewSessions",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "User",
            "status": "PUBLIC",
            "uiName": "% New Sessions",
            "description": "The percentage of sessions by people who had never visited your property before.",
            "calculation": "ga:newUsers / ga:sessions"
        }
    },
    "ga:percentNewVisits": {
        "id": "ga:percentNewVisits",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:percentNewSessions",
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "User",
            "status": "DEPRECATED",
            "uiName": "% New Sessions",
            "description": "The percentage of sessions by people who had never visited your property before.",
            "calculation": "ga:newUsers / ga:sessions"
        }
    },
    "ga:sessionDurationBucket": {
        "id": "ga:sessionDurationBucket",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Session",
            "status": "PUBLIC",
            "uiName": "Session Duration",
            "description": "The length of a session on your property measured in seconds and reported in second increments. The value returned is a string.",
            "allowedInSegments": "true"
        }
    },
    "ga:visitLength": {
        "id": "ga:visitLength",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:sessionDurationBucket",
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Session",
            "status": "DEPRECATED",
            "uiName": "Session Duration",
            "description": "The length of a session on your property measured in seconds and reported in second increments. The value returned is a string.",
            "allowedInSegments": "true"
        }
    },
    "ga:sessions": {
        "id": "ga:sessions",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Session",
            "status": "PUBLIC",
            "uiName": "Sessions",
            "description": "Counts the total number of sessions.",
            "allowedInSegments": "true"
        }
    },
    "ga:visits": {
        "id": "ga:visits",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:sessions",
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Session",
            "status": "DEPRECATED",
            "uiName": "Sessions",
            "description": "Counts the total number of sessions.",
            "allowedInSegments": "true"
        }
    },
    "ga:bounces": {
        "id": "ga:bounces",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Session",
            "status": "PUBLIC",
            "uiName": "Bounces",
            "description": "The total number of single page (or single engagement hit) sessions for your property.",
            "allowedInSegments": "true"
        }
    },
    "ga:entranceBounceRate": {
        "id": "ga:entranceBounceRate",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:bounceRate",
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Session",
            "status": "DEPRECATED",
            "uiName": "Bounce Rate",
            "description": "This dimension is deprecated and will be removed soon. Please use ga:bounceRate instead.",
            "calculation": "ga:bounces / ga:entrances"
        }
    },
    "ga:bounceRate": {
        "id": "ga:bounceRate",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Session",
            "status": "PUBLIC",
            "uiName": "Bounce Rate",
            "description": "The percentage of single-page session (i.e., session in which the person left your property from the first page).",
            "calculation": "ga:bounces / ga:sessions"
        }
    },
    "ga:visitBounceRate": {
        "id": "ga:visitBounceRate",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:bounceRate",
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Session",
            "status": "DEPRECATED",
            "uiName": "Bounce Rate",
            "description": "The percentage of single-page session (i.e., session in which the person left your property from the first page).",
            "calculation": "ga:bounces / ga:sessions"
        }
    },
    "ga:sessionDuration": {
        "id": "ga:sessionDuration",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "TIME",
            "group": "Session",
            "status": "PUBLIC",
            "uiName": "Session Duration",
            "description": "The total duration of user sessions represented in total seconds.",
            "allowedInSegments": "true"
        }
    },
    "ga:timeOnSite": {
        "id": "ga:timeOnSite",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:sessionDuration",
            "type": "METRIC",
            "dataType": "TIME",
            "group": "Session",
            "status": "DEPRECATED",
            "uiName": "Session Duration",
            "description": "The total duration of user sessions represented in total seconds.",
            "allowedInSegments": "true"
        }
    },
    "ga:avgSessionDuration": {
        "id": "ga:avgSessionDuration",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "TIME",
            "group": "Session",
            "status": "PUBLIC",
            "uiName": "Avg. Session Duration",
            "description": "The average duration of user sessions represented in total seconds.",
            "calculation": "ga:sessionDuration / ga:sessions"
        }
    },
    "ga:avgTimeOnSite": {
        "id": "ga:avgTimeOnSite",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:avgSessionDuration",
            "type": "METRIC",
            "dataType": "TIME",
            "group": "Session",
            "status": "DEPRECATED",
            "uiName": "Avg. Session Duration",
            "description": "The average duration of user sessions represented in total seconds.",
            "calculation": "ga:sessionDuration / ga:sessions"
        }
    },
    "ga:referralPath": {
        "id": "ga:referralPath",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Traffic Sources",
            "status": "PUBLIC",
            "uiName": "Referral Path",
            "description": "The path of the referring URL (e.g. document.referrer). If someone places a link to your property on their website, this element contains the path of the page that contains the referring link.",
            "allowedInSegments": "true"
        }
    },
    "ga:fullReferrer": {
        "id": "ga:fullReferrer",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Traffic Sources",
            "status": "PUBLIC",
            "uiName": "Full Referrer",
            "description": "The full referring URL including the hostname and path."
        }
    },
    "ga:campaign": {
        "id": "ga:campaign",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Traffic Sources",
            "status": "PUBLIC",
            "uiName": "Campaign",
            "description": "When using manual campaign tracking, the value of the utm_campaign campaign tracking parameter. When using AdWords autotagging, the name(s) of the online ad campaign that you use for your property. Otherwise the value (not set) is used.",
            "allowedInSegments": "true"
        }
    },
    "ga:source": {
        "id": "ga:source",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Traffic Sources",
            "status": "PUBLIC",
            "uiName": "Source",
            "description": "The source of referrals to your property. When using manual campaign tracking, the value of the utm_source campaign tracking parameter. When using AdWords autotagging, the value is google. Otherwise the domain of the source referring the user to your property (e.g. document.referrer). The value may also contain a port address. If the user arrived without a referrer, the value is (direct)",
            "allowedInSegments": "true"
        }
    },
    "ga:medium": {
        "id": "ga:medium",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Traffic Sources",
            "status": "PUBLIC",
            "uiName": "Medium",
            "description": "The type of referrals to your property. When using manual campaign tracking, the value of the utm_medium campaign tracking parameter. When using AdWords autotagging, the value is ppc. If the user comes from a search engine detected by Google Analytics, the value is organic. If the referrer is not a search engine, the value is referral. If the users came directly to the property, and document.referrer is empty, the value is (none).",
            "allowedInSegments": "true"
        }
    },
    "ga:sourceMedium": {
        "id": "ga:sourceMedium",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Traffic Sources",
            "status": "PUBLIC",
            "uiName": "Source / Medium",
            "description": "Combined values of ga:source and ga:medium.",
            "allowedInSegments": "true"
        }
    },
    "ga:keyword": {
        "id": "ga:keyword",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Traffic Sources",
            "status": "PUBLIC",
            "uiName": "Keyword",
            "description": "When using manual campaign tracking, the value of the utm_term campaign tracking parameter. When using AdWords autotagging or if a user used organic search to reach your property, the keywords used by users to reach your property. Otherwise the value is (not set).",
            "allowedInSegments": "true"
        }
    },
    "ga:adContent": {
        "id": "ga:adContent",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Traffic Sources",
            "status": "PUBLIC",
            "uiName": "Ad Content",
            "description": "When using manual campaign tracking, the value of the utm_content campaign tracking parameter. When using AdWords autotagging, the first line of the text for your online Ad campaign. If you are using mad libs for your AdWords content, this field displays the keywords you provided for the mad libs keyword match. Otherwise the value is (not set)",
            "allowedInSegments": "true"
        }
    },
    "ga:socialNetwork": {
        "id": "ga:socialNetwork",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Traffic Sources",
            "status": "PUBLIC",
            "uiName": "Social Network",
            "description": "Name of the social network. This can be related to the referring social network for traffic sources, or to the social network for social data hub activities. E.g. Google+, Blogger, etc."
        }
    },
    "ga:hasSocialSourceReferral": {
        "id": "ga:hasSocialSourceReferral",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Traffic Sources",
            "status": "PUBLIC",
            "uiName": "Social Source Referral",
            "description": "Indicates sessions that arrived to the property from a social source. The possible values are Yes or No where the first letter is capitalized."
        }
    },
    "ga:organicSearches": {
        "id": "ga:organicSearches",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Traffic Sources",
            "status": "PUBLIC",
            "uiName": "Organic Searches",
            "description": "The number of organic searches that happened within a session. This metric is search engine agnostic."
        }
    },
    "ga:adGroup": {
        "id": "ga:adGroup",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Ad Group",
            "description": "The name of your AdWords ad group.",
            "allowedInSegments": "true"
        }
    },
    "ga:adSlot": {
        "id": "ga:adSlot",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Ad Slot",
            "description": "The location of the advertisement on the hosting page (Top, RHS, or not set).",
            "allowedInSegments": "true"
        }
    },
    "ga:adSlotPosition": {
        "id": "ga:adSlotPosition",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Ad Slot Position",
            "description": "The ad slot positions in which your AdWords ads appeared (1-8).",
            "allowedInSegments": "true"
        }
    },
    "ga:adDistributionNetwork": {
        "id": "ga:adDistributionNetwork",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Ad Distribution Network",
            "description": "The networks used to deliver your ads (Content, Search, Search partners, etc.)."
        }
    },
    "ga:adMatchType": {
        "id": "ga:adMatchType",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Query Match Type",
            "description": "The match types applied for the search term the user had input(Phrase, Exact, Broad, etc.). Ads on the content network are identified as \"Content network\"."
        }
    },
    "ga:adKeywordMatchType": {
        "id": "ga:adKeywordMatchType",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Keyword Match Type",
            "description": "The match types applied to your keywords (Phrase, Exact, Broad)."
        }
    },
    "ga:adMatchedQuery": {
        "id": "ga:adMatchedQuery",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Matched Search Query",
            "description": "The search queries that triggered impressions of your AdWords ads."
        }
    },
    "ga:adPlacementDomain": {
        "id": "ga:adPlacementDomain",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Placement Domain",
            "description": "The domains where your ads on the content network were placed."
        }
    },
    "ga:adPlacementUrl": {
        "id": "ga:adPlacementUrl",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Placement URL",
            "description": "The URLs where your ads on the content network were placed."
        }
    },
    "ga:adFormat": {
        "id": "ga:adFormat",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Ad Format",
            "description": "Your AdWords ad formats (Text, Image, Flash, Video, etc.)."
        }
    },
    "ga:adTargetingType": {
        "id": "ga:adTargetingType",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Targeting Type",
            "description": "How your AdWords ads were targeted (keyword, placement, and vertical targeting, etc.)."
        }
    },
    "ga:adTargetingOption": {
        "id": "ga:adTargetingOption",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Placement Type",
            "description": "How you manage your ads on the content network. Values are Automatic placements or Managed placements."
        }
    },
    "ga:adDisplayUrl": {
        "id": "ga:adDisplayUrl",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Display URL",
            "description": "The URLs your AdWords ads displayed."
        }
    },
    "ga:adDestinationUrl": {
        "id": "ga:adDestinationUrl",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Destination URL",
            "description": "The URLs to which your AdWords ads referred traffic."
        }
    },
    "ga:adwordsCustomerID": {
        "id": "ga:adwordsCustomerID",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "AdWords Customer ID",
            "description": "A string. Corresponds to AdWords API AccountInfo.customerId."
        }
    },
    "ga:adwordsCampaignID": {
        "id": "ga:adwordsCampaignID",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "AdWords Campaign ID",
            "description": "A string. Corresponds to AdWords API Campaign.id."
        }
    },
    "ga:adwordsAdGroupID": {
        "id": "ga:adwordsAdGroupID",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "AdWords Ad Group ID",
            "description": "A string. Corresponds to AdWords API AdGroup.id."
        }
    },
    "ga:adwordsCreativeID": {
        "id": "ga:adwordsCreativeID",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "AdWords Creative ID",
            "description": "A string. Corresponds to AdWords API Ad.id."
        }
    },
    "ga:adwordsCriteriaID": {
        "id": "ga:adwordsCriteriaID",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "AdWords Criteria ID",
            "description": "A string. Corresponds to AdWords API Criterion.id."
        }
    },
    "ga:impressions": {
        "id": "ga:impressions",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Impressions",
            "description": "Total number of campaign impressions."
        }
    },
    "ga:adClicks": {
        "id": "ga:adClicks",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Clicks",
            "description": "The total number of times users have clicked on an ad to reach your property."
        }
    },
    "ga:adCost": {
        "id": "ga:adCost",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Cost",
            "description": "Derived cost for the advertising campaign. The currency for this value is based on the currency that you set in your AdWords account."
        }
    },
    "ga:CPM": {
        "id": "ga:CPM",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "CPM",
            "description": "Cost per thousand impressions.",
            "calculation": "ga:adCost / (ga:impressions / 1000)"
        }
    },
    "ga:CPC": {
        "id": "ga:CPC",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "CPC",
            "description": "Cost to advertiser per click.",
            "calculation": "ga:adCost / ga:adClicks"
        }
    },
    "ga:CTR": {
        "id": "ga:CTR",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "CTR",
            "description": "Click-through-rate for your ad. This is equal to the number of clicks divided by the number of impressions for your ad (e.g. how many times users clicked on one of your ads where that ad appeared).",
            "calculation": "ga:adClicks / ga:impressions"
        }
    },
    "ga:costPerTransaction": {
        "id": "ga:costPerTransaction",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Cost per Transaction",
            "description": "The cost per transaction for your property.",
            "calculation": "(ga:adCost) / (ga:transactions)"
        }
    },
    "ga:costPerGoalConversion": {
        "id": "ga:costPerGoalConversion",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Cost per Goal Conversion",
            "description": "The cost per goal conversion for your property.",
            "calculation": "(ga:adCost) / (ga:goalCompletionsAll)"
        }
    },
    "ga:costPerConversion": {
        "id": "ga:costPerConversion",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Cost per Conversion",
            "description": "The cost per conversion (including ecommerce and goal conversions) for your property.",
            "calculation": "(ga:adCost) / (ga:transactions + ga:goalCompletionsAll)"
        }
    },
    "ga:RPC": {
        "id": "ga:RPC",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "RPC",
            "description": "RPC or revenue-per-click is the average revenue (from ecommerce sales and/or goal value) you received for each click on one of your search ads.",
            "calculation": "(ga:transactionRevenue + ga:goalValueAll) / ga:adClicks"
        }
    },
    "ga:ROI": {
        "id": "ga:ROI",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "ROI",
            "description": "Returns on Investment is overall transaction profit divided by derived advertising cost.",
            "calculation": "(ga:transactionRevenue + ga:goalValueAll - ga:adCost) / ga:adCost"
        }
    },
    "ga:margin": {
        "id": "ga:margin",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "Margin",
            "description": "The overall transaction profit margin.",
            "calculation": "(ga:transactionRevenue + ga:goalValueAll - ga:adCost) / (ga:transactionRevenue + ga:goalValueAll)"
        }
    },
    "ga:goalCompletionLocation": {
        "id": "ga:goalCompletionLocation",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal Completion Location",
            "description": "The page path or screen name that matched any destination type goal completion."
        }
    },
    "ga:goalPreviousStep1": {
        "id": "ga:goalPreviousStep1",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal Previous Step - 1",
            "description": "The page path or screen name that matched any destination type goal, one step prior to the goal completion location."
        }
    },
    "ga:goalPreviousStep2": {
        "id": "ga:goalPreviousStep2",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal Previous Step - 2",
            "description": "The page path or screen name that matched any destination type goal, two steps prior to the goal completion location."
        }
    },
    "ga:goalPreviousStep3": {
        "id": "ga:goalPreviousStep3",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal Previous Step - 3",
            "description": "The page path or screen name that matched any destination type goal, three steps prior to the goal completion location."
        }
    },
    "ga:goalXXStarts": {
        "id": "ga:goalXXStarts",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal 1 Starts",
            "description": "The total number of starts for the requested goal number.",
            "minTemplateIndex": "1",
            "maxTemplateIndex": "20",
            "allowedInSegments": "true"
        }
    },
    "ga:goalStartsAll": {
        "id": "ga:goalStartsAll",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal Starts",
            "description": "The total number of starts for all goals defined for your profile.",
            "allowedInSegments": "true"
        }
    },
    "ga:goalXXCompletions": {
        "id": "ga:goalXXCompletions",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal 1 Completions",
            "description": "The total number of completions for the requested goal number.",
            "minTemplateIndex": "1",
            "maxTemplateIndex": "20",
            "allowedInSegments": "true"
        }
    },
    "ga:goalCompletionsAll": {
        "id": "ga:goalCompletionsAll",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal Completions",
            "description": "The total number of completions for all goals defined for your profile.",
            "allowedInSegments": "true"
        }
    },
    "ga:goalXXValue": {
        "id": "ga:goalXXValue",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal 1 Value",
            "description": "The total numeric value for the requested goal number.",
            "minTemplateIndex": "1",
            "maxTemplateIndex": "20",
            "allowedInSegments": "true"
        }
    },
    "ga:goalValueAll": {
        "id": "ga:goalValueAll",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal Value",
            "description": "The total numeric value for all goals defined for your profile.",
            "allowedInSegments": "true"
        }
    },
    "ga:goalValuePerSession": {
        "id": "ga:goalValuePerSession",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Per Session Goal Value",
            "description": "The average goal value of a session on your property.",
            "calculation": "ga:goalValueAll / ga:sessions"
        }
    },
    "ga:goalValuePerVisit": {
        "id": "ga:goalValuePerVisit",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:goalValuePerSession",
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Goal Conversions",
            "status": "DEPRECATED",
            "uiName": "Per Session Goal Value",
            "description": "The average goal value of a session on your property.",
            "calculation": "ga:goalValueAll / ga:sessions"
        }
    },
    "ga:goalXXConversionRate": {
        "id": "ga:goalXXConversionRate",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal 1 Conversion Rate",
            "description": "The percentage of sessions which resulted in a conversion to the requested goal number.",
            "calculation": "ga:goalXXCompletions / ga:sessions",
            "minTemplateIndex": "1",
            "maxTemplateIndex": "20"
        }
    },
    "ga:goalConversionRateAll": {
        "id": "ga:goalConversionRateAll",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal Conversion Rate",
            "description": "The percentage of sessions which resulted in a conversion to at least one of your goals.",
            "calculation": "ga:goalCompletionsAll / ga:sessions"
        }
    },
    "ga:goalXXAbandons": {
        "id": "ga:goalXXAbandons",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal 1 Abandoned Funnels",
            "description": "The number of times users started conversion activity on the requested goal number without actually completing it.",
            "calculation": "(ga:goalXXStarts - ga:goalXXCompletions)",
            "minTemplateIndex": "1",
            "maxTemplateIndex": "20"
        }
    },
    "ga:goalAbandonsAll": {
        "id": "ga:goalAbandonsAll",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Abandoned Funnels",
            "description": "The overall number of times users started goals without actually completing them.",
            "calculation": "(ga:goalStartsAll - ga:goalCompletionsAll)"
        }
    },
    "ga:goalXXAbandonRate": {
        "id": "ga:goalXXAbandonRate",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Goal 1 Abandonment Rate",
            "description": "The rate at which the requested goal number was abandoned.",
            "calculation": "((ga:goalXXStarts - ga:goalXXCompletions)) / (ga:goalXXStarts)",
            "minTemplateIndex": "1",
            "maxTemplateIndex": "20"
        }
    },
    "ga:goalAbandonRateAll": {
        "id": "ga:goalAbandonRateAll",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Goal Conversions",
            "status": "PUBLIC",
            "uiName": "Total Abandonment Rate",
            "description": "The rate at which goals were abandoned.",
            "calculation": "((ga:goalStartsAll - ga:goalCompletionsAll)) / (ga:goalStartsAll)"
        }
    },
    "ga:browser": {
        "id": "ga:browser",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Platform or Device",
            "status": "PUBLIC",
            "uiName": "Browser",
            "description": "The names of browsers used by users to your website. For example, Internet Explorer or Firefox.",
            "allowedInSegments": "true"
        }
    },
    "ga:browserVersion": {
        "id": "ga:browserVersion",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Platform or Device",
            "status": "PUBLIC",
            "uiName": "Browser Version",
            "description": "The browser versions used by users to your website. For example, 2.0.0.14",
            "allowedInSegments": "true"
        }
    },
    "ga:operatingSystem": {
        "id": "ga:operatingSystem",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Platform or Device",
            "status": "PUBLIC",
            "uiName": "Operating System",
            "description": "The operating system used by your users. For example, Windows, Linux , Macintosh, iPhone, iPod.",
            "allowedInSegments": "true"
        }
    },
    "ga:operatingSystemVersion": {
        "id": "ga:operatingSystemVersion",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Platform or Device",
            "status": "PUBLIC",
            "uiName": "Operating System Version",
            "description": "The version of the operating system used by your users, such as XP for Windows or PPC for Macintosh.",
            "allowedInSegments": "true"
        }
    },
    "ga:isMobile": {
        "id": "ga:isMobile",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Platform or Device",
            "status": "DEPRECATED",
            "uiName": "Mobile (Including Tablet)",
            "description": "This dimension is deprecated and will be removed soon. Please use ga:deviceCategory instead (e.g., ga:deviceCategory==mobile).",
            "allowedInSegments": "true"
        }
    },
    "ga:isTablet": {
        "id": "ga:isTablet",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Platform or Device",
            "status": "DEPRECATED",
            "uiName": "Tablet",
            "description": "This dimension is deprecated and will be removed soon. Please use ga:deviceCategory instead (e.g., ga:deviceCategory==tablet).",
            "allowedInSegments": "true"
        }
    },
    "ga:mobileDeviceBranding": {
        "id": "ga:mobileDeviceBranding",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Platform or Device",
            "status": "PUBLIC",
            "uiName": "Mobile Device Branding",
            "description": "Mobile manufacturer or branded name.",
            "allowedInSegments": "true"
        }
    },
    "ga:mobileDeviceModel": {
        "id": "ga:mobileDeviceModel",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Platform or Device",
            "status": "PUBLIC",
            "uiName": "Mobile Device Model",
            "description": "Mobile device model",
            "allowedInSegments": "true"
        }
    },
    "ga:mobileInputSelector": {
        "id": "ga:mobileInputSelector",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Platform or Device",
            "status": "PUBLIC",
            "uiName": "Mobile Input Selector",
            "description": "Selector used on the mobile device (e.g.: touchscreen, joystick, clickwheel, stylus).",
            "allowedInSegments": "true"
        }
    },
    "ga:mobileDeviceInfo": {
        "id": "ga:mobileDeviceInfo",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Platform or Device",
            "status": "PUBLIC",
            "uiName": "Mobile Device Info",
            "description": "The branding, model, and marketing name used to identify the mobile device.",
            "allowedInSegments": "true"
        }
    },
    "ga:mobileDeviceMarketingName": {
        "id": "ga:mobileDeviceMarketingName",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Platform or Device",
            "status": "PUBLIC",
            "uiName": "Mobile Device Marketing Name",
            "description": "The marketing name used for the mobile device.",
            "allowedInSegments": "true"
        }
    },
    "ga:deviceCategory": {
        "id": "ga:deviceCategory",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Platform or Device",
            "status": "PUBLIC",
            "uiName": "Device Category",
            "description": "The type of device: desktop, tablet, or mobile.",
            "allowedInSegments": "true"
        }
    },
    "ga:continent": {
        "id": "ga:continent",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Geo Network",
            "status": "PUBLIC",
            "uiName": "Continent",
            "description": "The continents of property users, derived from IP addresses.",
            "allowedInSegments": "true"
        }
    },
    "ga:subContinent": {
        "id": "ga:subContinent",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Geo Network",
            "status": "PUBLIC",
            "uiName": "Sub Continent Region",
            "description": "The sub-continent of users, derived from IP addresses. For example, Polynesia or Northern Europe.",
            "allowedInSegments": "true"
        }
    },
    "ga:country": {
        "id": "ga:country",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Geo Network",
            "status": "PUBLIC",
            "uiName": "Country / Territory",
            "description": "The country of users, derived from IP addresses.",
            "allowedInSegments": "true"
        }
    },
    "ga:region": {
        "id": "ga:region",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Geo Network",
            "status": "PUBLIC",
            "uiName": "Region",
            "description": "The region of users to your property, derived from IP addresses. In the U.S., a region is a state, such as New York.",
            "allowedInSegments": "true"
        }
    },
    "ga:metro": {
        "id": "ga:metro",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Geo Network",
            "status": "PUBLIC",
            "uiName": "Metro",
            "description": "The Designated Market Area (DMA) from where traffic arrived on your property.",
            "allowedInSegments": "true"
        }
    },
    "ga:city": {
        "id": "ga:city",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Geo Network",
            "status": "PUBLIC",
            "uiName": "City",
            "description": "The cities of property users, derived from IP addresses.",
            "allowedInSegments": "true"
        }
    },
    "ga:latitude": {
        "id": "ga:latitude",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Geo Network",
            "status": "PUBLIC",
            "uiName": "Latitude",
            "description": "The approximate latitude of the user's city. Derived from IP address. Locations north of the equator are represented by positive values and locations south of the equator by negative values."
        }
    },
    "ga:longitude": {
        "id": "ga:longitude",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Geo Network",
            "status": "PUBLIC",
            "uiName": "Longitude",
            "description": "The approximate longitude of the user's city. Derived from IP address. Locations east of the meridian are represented by positive values and locations west of the meridian by negative values."
        }
    },
    "ga:networkDomain": {
        "id": "ga:networkDomain",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Geo Network",
            "status": "PUBLIC",
            "uiName": "Network Domain",
            "description": "The domain name of the ISPs used by users to your property. This is derived from the domain name registered to the IP address.",
            "allowedInSegments": "true"
        }
    },
    "ga:networkLocation": {
        "id": "ga:networkLocation",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Geo Network",
            "status": "PUBLIC",
            "uiName": "Service Provider",
            "description": "The name of service providers used to reach your property. For example, if most users to your website come via the major service providers for cable internet, you will see the names of those cable service providers in this element.",
            "allowedInSegments": "true"
        }
    },
    "ga:flashVersion": {
        "id": "ga:flashVersion",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "System",
            "status": "PUBLIC",
            "uiName": "Flash Version",
            "description": "The versions of Flash supported by users' browsers, including minor versions.",
            "allowedInSegments": "true"
        }
    },
    "ga:javaEnabled": {
        "id": "ga:javaEnabled",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "System",
            "status": "PUBLIC",
            "uiName": "Java Support",
            "description": "Indicates Java support for users' browsers. The possible values are Yes or No where the first letter must be capitalized.",
            "allowedInSegments": "true"
        }
    },
    "ga:language": {
        "id": "ga:language",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "System",
            "status": "PUBLIC",
            "uiName": "Language",
            "description": "The language provided by the HTTP Request for the browser. Values are given as an ISO-639 code (e.g. en-gb for British English).",
            "allowedInSegments": "true"
        }
    },
    "ga:screenColors": {
        "id": "ga:screenColors",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "System",
            "status": "PUBLIC",
            "uiName": "Screen Colors",
            "description": "The color depth of users' monitors, as retrieved from the DOM of the user's browser. For example 4-bit, 8-bit, 24-bit, or undefined-bit.",
            "allowedInSegments": "true"
        }
    },
    "ga:sourcePropertyId": {
        "id": "ga:sourcePropertyId",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "System",
            "status": "PUBLIC",
            "uiName": "Source Property Tracking ID",
            "description": "Source property tracking ID of roll-up properties. This is valid only for roll-up properties.",
            "allowedInSegments": "true"
        }
    },
    "ga:sourcePropertyName": {
        "id": "ga:sourcePropertyName",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "System",
            "status": "PUBLIC",
            "uiName": "Source Property Display Name",
            "description": "Source property display name of roll-up properties. This is valid only for roll-up properties.",
            "allowedInSegments": "true"
        }
    },
    "ga:screenResolution": {
        "id": "ga:screenResolution",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "System",
            "status": "PUBLIC",
            "uiName": "Screen Resolution",
            "description": "The screen resolution of users' screens. For example: 1024x738.",
            "allowedInSegments": "true"
        }
    },
    "ga:socialActivityEndorsingUrl": {
        "id": "ga:socialActivityEndorsingUrl",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Activities",
            "status": "PUBLIC",
            "uiName": "Endorsing URL",
            "description": "For a social data hub activity, this value represents the URL of the social activity (e.g. the Google+ post URL, the blog comment URL, etc.)"
        }
    },
    "ga:socialActivityDisplayName": {
        "id": "ga:socialActivityDisplayName",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Activities",
            "status": "PUBLIC",
            "uiName": "Display Name",
            "description": "For a social data hub activity, this value represents the title of the social activity posted by the social network user."
        }
    },
    "ga:socialActivityPost": {
        "id": "ga:socialActivityPost",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Activities",
            "status": "PUBLIC",
            "uiName": "Social Activity Post",
            "description": "For a social data hub activity, this value represents the content of the social activity posted by the social network user (e.g. The message content of a Google+ post)"
        }
    },
    "ga:socialActivityTimestamp": {
        "id": "ga:socialActivityTimestamp",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Activities",
            "status": "PUBLIC",
            "uiName": "Social Activity Timestamp",
            "description": "For a social data hub activity, this value represents when the social activity occurred on the social network."
        }
    },
    "ga:socialActivityUserHandle": {
        "id": "ga:socialActivityUserHandle",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Activities",
            "status": "PUBLIC",
            "uiName": "Social User Handle",
            "description": "For a social data hub activity, this value represents the social network handle (e.g. name or ID) of the user who initiated the social activity."
        }
    },
    "ga:socialActivityUserPhotoUrl": {
        "id": "ga:socialActivityUserPhotoUrl",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Activities",
            "status": "PUBLIC",
            "uiName": "User Photo URL",
            "description": "For a social data hub activity, this value represents the URL of the photo associated with the user's social network profile."
        }
    },
    "ga:socialActivityUserProfileUrl": {
        "id": "ga:socialActivityUserProfileUrl",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Activities",
            "status": "PUBLIC",
            "uiName": "User Profile URL",
            "description": "For a social data hub activity, this value represents the URL of the associated user's social network profile."
        }
    },
    "ga:socialActivityContentUrl": {
        "id": "ga:socialActivityContentUrl",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Activities",
            "status": "PUBLIC",
            "uiName": "Shared URL",
            "description": "For a social data hub activity, this value represents the URL shared by the associated social network user."
        }
    },
    "ga:socialActivityTagsSummary": {
        "id": "ga:socialActivityTagsSummary",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Activities",
            "status": "PUBLIC",
            "uiName": "Social Tags Summary",
            "description": "For a social data hub activity, this is a comma-separated set of tags associated with the social activity."
        }
    },
    "ga:socialActivityAction": {
        "id": "ga:socialActivityAction",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Activities",
            "status": "PUBLIC",
            "uiName": "Originating Social Action",
            "description": "For a social data hub activity, this value represents the type of social action associated with the activity (e.g. vote, comment, +1, etc.)."
        }
    },
    "ga:socialActivityNetworkAction": {
        "id": "ga:socialActivityNetworkAction",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Activities",
            "status": "PUBLIC",
            "uiName": "Social Network and Action",
            "description": "For a social data hub activity, this value represents the type of social action and the social network where the activity originated."
        }
    },
    "ga:socialActivities": {
        "id": "ga:socialActivities",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Social Activities",
            "status": "PUBLIC",
            "uiName": "Data Hub Activities",
            "description": "The count of activities where a content URL was shared / mentioned on a social data hub partner network."
        }
    },
    "ga:hostname": {
        "id": "ga:hostname",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Hostname",
            "description": "The hostname from which the tracking request was made.",
            "allowedInSegments": "true"
        }
    },
    "ga:pagePath": {
        "id": "ga:pagePath",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Page",
            "description": "A page on your website specified by path and/or query parameters. Use in conjunction with hostname to get the full URL of the page.",
            "allowedInSegments": "true"
        }
    },
    "ga:pagePathLevel1": {
        "id": "ga:pagePathLevel1",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Page path level 1",
            "description": "This dimension rolls up all the page paths in the first hierarchical level in pagePath."
        }
    },
    "ga:pagePathLevel2": {
        "id": "ga:pagePathLevel2",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Page path level 2",
            "description": "This dimension rolls up all the page paths in the second hierarchical level in pagePath."
        }
    },
    "ga:pagePathLevel3": {
        "id": "ga:pagePathLevel3",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Page path level 3",
            "description": "This dimension rolls up all the page paths in the third hierarchical level in pagePath."
        }
    },
    "ga:pagePathLevel4": {
        "id": "ga:pagePathLevel4",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Page path level 4",
            "description": "This dimension rolls up all the page paths into hierarchical levels. Up to 4 pagePath levels maybe specified. All additional levels in the pagePath hierarchy are also rolled up in this dimension."
        }
    },
    "ga:pageTitle": {
        "id": "ga:pageTitle",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Page Title",
            "description": "The title of a page. Keep in mind that multiple pages might have the same page title.",
            "allowedInSegments": "true"
        }
    },
    "ga:landingPagePath": {
        "id": "ga:landingPagePath",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Landing Page",
            "description": "The first page in a user's session, or landing page.",
            "allowedInSegments": "true"
        }
    },
    "ga:secondPagePath": {
        "id": "ga:secondPagePath",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Second Page",
            "description": "The second page in a user's session."
        }
    },
    "ga:exitPagePath": {
        "id": "ga:exitPagePath",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Exit Page",
            "description": "The last page in a user's session, or exit page.",
            "allowedInSegments": "true"
        }
    },
    "ga:previousPagePath": {
        "id": "ga:previousPagePath",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Previous Page Path",
            "description": "A page on your property that was visited before another page on the same property. Typically used with the pagePath dimension."
        }
    },
    "ga:nextPagePath": {
        "id": "ga:nextPagePath",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Next Page Path",
            "description": "A page on your website that was visited after another page on your website. Typically used with the previousPagePath dimension."
        }
    },
    "ga:pageDepth": {
        "id": "ga:pageDepth",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Page Depth",
            "description": "The number of pages visited by users during a session. The value is a histogram that counts pageviews across a range of possible values. In this calculation, all sessions will have at least one pageview, and some percentage of sessions will have more.",
            "allowedInSegments": "true"
        }
    },
    "ga:pageValue": {
        "id": "ga:pageValue",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Page Value",
            "description": "The average value of this page or set of pages. Page Value is (ga:transactionRevenue + ga:goalValueAll) / ga:uniquePageviews (for the page or set of pages)."
        }
    },
    "ga:entrances": {
        "id": "ga:entrances",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Entrances",
            "description": "The number of entrances to your property measured as the first pageview in a session. Typically used with landingPagePath",
            "allowedInSegments": "true"
        }
    },
    "ga:entranceRate": {
        "id": "ga:entranceRate",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Entrances / Pageviews",
            "description": "The percentage of pageviews in which this page was the entrance.",
            "calculation": "ga:entrances / ga:pageviews"
        }
    },
    "ga:pageviews": {
        "id": "ga:pageviews",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Pageviews",
            "description": "The total number of pageviews for your property.",
            "allowedInSegments": "true"
        }
    },
    "ga:pageviewsPerSession": {
        "id": "ga:pageviewsPerSession",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Pages / Session",
            "description": "The average number of pages viewed during a session on your property. Repeated views of a single page are counted.",
            "calculation": "ga:pageviews / ga:sessions"
        }
    },
    "ga:pageviewsPerVisit": {
        "id": "ga:pageviewsPerVisit",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:pageviewsPerSession",
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Page Tracking",
            "status": "DEPRECATED",
            "uiName": "Pages / Session",
            "description": "The average number of pages viewed during a session on your property. Repeated views of a single page are counted.",
            "calculation": "ga:pageviews / ga:sessions"
        }
    },
    "ga:uniquePageviews": {
        "id": "ga:uniquePageviews",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Unique Pageviews",
            "description": "The number of different (unique) pages within a session. This takes into both the pagePath and pageTitle to determine uniqueness.",
            "allowedInSegments": "true"
        }
    },
    "ga:timeOnPage": {
        "id": "ga:timeOnPage",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "TIME",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Time on Page",
            "description": "How long a user spent on a particular page in seconds. Calculated by subtracting the initial view time for a particular page from the initial view time for a subsequent page. Thus, this metric does not apply to exit pages for your property.",
            "allowedInSegments": "true"
        }
    },
    "ga:avgTimeOnPage": {
        "id": "ga:avgTimeOnPage",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "TIME",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Avg. Time on Page",
            "description": "The average amount of time users spent viewing this page or a set of pages.",
            "calculation": "ga:timeOnPage / (ga:pageviews - ga:exits)"
        }
    },
    "ga:exits": {
        "id": "ga:exits",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "Exits",
            "description": "The number of exits from your property.",
            "allowedInSegments": "true"
        }
    },
    "ga:exitRate": {
        "id": "ga:exitRate",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Page Tracking",
            "status": "PUBLIC",
            "uiName": "% Exit",
            "description": "The percentage of exits from your property that occurred out of the total page views.",
            "calculation": "ga:exits / (ga:pageviews + ga:screenviews)"
        }
    },
    "ga:searchUsed": {
        "id": "ga:searchUsed",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Site Search Status",
            "description": "A boolean to distinguish whether internal search was used in a session. Values are Visits With Site Search and Visits Without Site Search.",
            "allowedInSegments": "true"
        }
    },
    "ga:searchKeyword": {
        "id": "ga:searchKeyword",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Search Term",
            "description": "Search terms used by users within your property.",
            "allowedInSegments": "true"
        }
    },
    "ga:searchKeywordRefinement": {
        "id": "ga:searchKeywordRefinement",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Refined Keyword",
            "description": "Subsequent keyword search terms or strings entered by users after a given initial string search.",
            "allowedInSegments": "true"
        }
    },
    "ga:searchCategory": {
        "id": "ga:searchCategory",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Site Search Category",
            "description": "The categories used for the internal search if you have this enabled for your profile. For example, you might have product categories such as electronics, furniture, or clothing.",
            "allowedInSegments": "true"
        }
    },
    "ga:searchStartPage": {
        "id": "ga:searchStartPage",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Start Page",
            "description": "A page where the user initiated an internal search on your property."
        }
    },
    "ga:searchDestinationPage": {
        "id": "ga:searchDestinationPage",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Destination Page",
            "description": "A page that the user visited after performing an internal search on your property."
        }
    },
    "ga:searchResultViews": {
        "id": "ga:searchResultViews",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Results Pageviews",
            "description": "The number of times a search result page was viewed after performing a search."
        }
    },
    "ga:searchUniques": {
        "id": "ga:searchUniques",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Total Unique Searches",
            "description": "The total number of unique keywords from internal searches within a session. For example if \"shoes\" was searched for 3 times in a session, it will be only counted once.",
            "allowedInSegments": "true"
        }
    },
    "ga:avgSearchResultViews": {
        "id": "ga:avgSearchResultViews",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Results Pageviews / Search",
            "description": "The average number of times people viewed a search results page after performing a search.",
            "calculation": "ga:searchResultViews / ga:searchUniques"
        }
    },
    "ga:searchSessions": {
        "id": "ga:searchSessions",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Sessions with Search",
            "description": "The total number of sessions that included an internal search",
            "allowedInSegments": "true"
        }
    },
    "ga:searchVisits": {
        "id": "ga:searchVisits",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:searchSessions",
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Internal Search",
            "status": "DEPRECATED",
            "uiName": "Sessions with Search",
            "description": "The total number of sessions that included an internal search",
            "allowedInSegments": "true"
        }
    },
    "ga:percentSessionsWithSearch": {
        "id": "ga:percentSessionsWithSearch",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "% Sessions with Search",
            "description": "The percentage of sessions with search.",
            "calculation": "ga:searchSessions / ga:sessions"
        }
    },
    "ga:percentVisitsWithSearch": {
        "id": "ga:percentVisitsWithSearch",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:percentSessionsWithSearch",
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Internal Search",
            "status": "DEPRECATED",
            "uiName": "% Sessions with Search",
            "description": "The percentage of sessions with search.",
            "calculation": "ga:searchSessions / ga:sessions"
        }
    },
    "ga:searchDepth": {
        "id": "ga:searchDepth",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Search Depth",
            "description": "The average number of subsequent page views made on your property after a use of your internal search feature.",
            "allowedInSegments": "true"
        }
    },
    "ga:avgSearchDepth": {
        "id": "ga:avgSearchDepth",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Search Depth",
            "description": "The average number of pages people viewed after performing a search on your property.",
            "calculation": "ga:searchDepth / ga:searchUniques"
        }
    },
    "ga:searchRefinements": {
        "id": "ga:searchRefinements",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Search Refinements",
            "description": "The total number of times a refinement (transition) occurs between internal search keywords within a session. For example if the sequence of keywords is: \"shoes\", \"shoes\", \"pants\", \"pants\", this metric will be one because the transition between \"shoes\" and \"pants\" is different.",
            "allowedInSegments": "true"
        }
    },
    "ga:percentSearchRefinements": {
        "id": "ga:percentSearchRefinements",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "% Search Refinements",
            "description": "The percentage of number of times a refinement (i.e., transition) occurs between internal search keywords within a session.",
            "calculation": "ga:searchRefinements / ga:searchResultViews"
        }
    },
    "ga:searchDuration": {
        "id": "ga:searchDuration",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "TIME",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Time after Search",
            "description": "The session duration on your property where a use of your internal search feature occurred.",
            "allowedInSegments": "true"
        }
    },
    "ga:avgSearchDuration": {
        "id": "ga:avgSearchDuration",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "TIME",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Time after Search",
            "description": "The average amount of time people spent on your property after searching.",
            "calculation": "ga:searchDuration / ga:searchUniques"
        }
    },
    "ga:searchExits": {
        "id": "ga:searchExits",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Search Exits",
            "description": "The number of exits on your site that occurred following a search result from your internal search feature.",
            "allowedInSegments": "true"
        }
    },
    "ga:searchExitRate": {
        "id": "ga:searchExitRate",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "% Search Exits",
            "description": "The percentage of searches that resulted in an immediate exit from your property.",
            "calculation": "ga:searchExits / ga:searchUniques"
        }
    },
    "ga:searchGoalXXConversionRate": {
        "id": "ga:searchGoalXXConversionRate",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Site Search Goal 1 Conversion Rate",
            "description": "The percentage of search sessions (i.e., sessions that included at least one search) which resulted in a conversion to the requested goal number.",
            "calculation": "ga:goalXXCompletions / ga:searchUniques",
            "minTemplateIndex": "1",
            "maxTemplateIndex": "20"
        }
    },
    "ga:searchGoalConversionRateAll": {
        "id": "ga:searchGoalConversionRateAll",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Site Search Goal Conversion Rate",
            "description": "The percentage of search sessions (i.e., sessions that included at least one search) which resulted in a conversion to at least one of your goals.",
            "calculation": "ga:goalCompletionsAll / ga:searchUniques"
        }
    },
    "ga:goalValueAllPerSearch": {
        "id": "ga:goalValueAllPerSearch",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Internal Search",
            "status": "PUBLIC",
            "uiName": "Per Search Goal Value",
            "description": "The average goal value of a search on your property.",
            "calculation": "ga:goalValueAll / ga:searchUniques"
        }
    },
    "ga:pageLoadTime": {
        "id": "ga:pageLoadTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Page Load Time (ms)",
            "description": "Total Page Load Time is the amount of time (in milliseconds) it takes for pages from the sample set to load, from initiation of the pageview (e.g. click on a page link) to load completion in the browser."
        }
    },
    "ga:pageLoadSample": {
        "id": "ga:pageLoadSample",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Page Load Sample",
            "description": "The sample set (or count) of pageviews used to calculate the average page load time."
        }
    },
    "ga:avgPageLoadTime": {
        "id": "ga:avgPageLoadTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Avg. Page Load Time (sec)",
            "description": "The average amount of time (in seconds) it takes for pages from the sample set to load, from initiation of the pageview (e.g. click on a page link) to load completion in the browser.",
            "calculation": "(ga:pageLoadTime / ga:pageLoadSample / 1000)"
        }
    },
    "ga:domainLookupTime": {
        "id": "ga:domainLookupTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Domain Lookup Time (ms)",
            "description": "The total amount of time (in milliseconds) spent in DNS lookup for this page among all samples."
        }
    },
    "ga:avgDomainLookupTime": {
        "id": "ga:avgDomainLookupTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Avg. Domain Lookup Time (sec)",
            "description": "The average amount of time (in seconds) spent in DNS lookup for this page.",
            "calculation": "(ga:domainLookupTime / ga:speedMetricsSample / 1000)"
        }
    },
    "ga:pageDownloadTime": {
        "id": "ga:pageDownloadTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Page Download Time (ms)",
            "description": "The total amount of time (in milliseconds) to download this page among all samples."
        }
    },
    "ga:avgPageDownloadTime": {
        "id": "ga:avgPageDownloadTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Avg. Page Download Time (sec)",
            "description": "The average amount of time (in seconds) to download this page.",
            "calculation": "(ga:pageDownloadTime / ga:speedMetricsSample / 1000)"
        }
    },
    "ga:redirectionTime": {
        "id": "ga:redirectionTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Redirection Time (ms)",
            "description": "The total amount of time (in milliseconds) spent in redirects before fetching this page among all samples. If there are no redirects, the value for this metric is expected to be 0."
        }
    },
    "ga:avgRedirectionTime": {
        "id": "ga:avgRedirectionTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Avg. Redirection Time (sec)",
            "description": "The average amount of time (in seconds) spent in redirects before fetching this page. If there are no redirects, the value for this metric is expected to be 0.",
            "calculation": "(ga:redirectionTime / ga:speedMetricsSample / 1000)"
        }
    },
    "ga:serverConnectionTime": {
        "id": "ga:serverConnectionTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Server Connection Time (ms)",
            "description": "The total amount of time (in milliseconds) spent in establishing TCP connection for this page among all samples."
        }
    },
    "ga:avgServerConnectionTime": {
        "id": "ga:avgServerConnectionTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Avg. Server Connection Time (sec)",
            "description": "The average amount of time (in seconds) spent in establishing TCP connection for this page.",
            "calculation": "(ga:serverConnectionTime / ga:speedMetricsSample / 1000)"
        }
    },
    "ga:serverResponseTime": {
        "id": "ga:serverResponseTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Server Response Time (ms)",
            "description": "The total amount of time (in milliseconds) your server takes to respond to a user request among all samples, including the network time from user's location to your server."
        }
    },
    "ga:avgServerResponseTime": {
        "id": "ga:avgServerResponseTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Avg. Server Response Time (sec)",
            "description": "The average amount of time (in seconds) your server takes to respond to a user request, including the network time from user's location to your server.",
            "calculation": "(ga:serverResponseTime / ga:speedMetricsSample / 1000)"
        }
    },
    "ga:speedMetricsSample": {
        "id": "ga:speedMetricsSample",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Speed Metrics Sample",
            "description": "The sample set (or count) of pageviews used to calculate the averages for site speed metrics. This metric is used in all site speed average calculations including avgDomainLookupTime, avgPageDownloadTime, avgRedirectionTime, avgServerConnectionTime, and avgServerResponseTime."
        }
    },
    "ga:domInteractiveTime": {
        "id": "ga:domInteractiveTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Document Interactive Time (ms)",
            "description": "The time the browser takes (in milliseconds) to parse the document (DOMInteractive), including the network time from the user's location to your server. At this time, the user can interact with the Document Object Model even though it is not fully loaded."
        }
    },
    "ga:avgDomInteractiveTime": {
        "id": "ga:avgDomInteractiveTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Avg. Document Interactive Time (sec)",
            "description": "The average time (in seconds) it takes the browser to parse the document and execute deferred and parser-inserted scripts including the network time from the user's location to your server.",
            "calculation": "(ga:domInteractiveTime / ga:domLatencyMetricsSample / 1000)"
        }
    },
    "ga:domContentLoadedTime": {
        "id": "ga:domContentLoadedTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Document Content Loaded Time (ms)",
            "description": "The time the browser takes (in milliseconds) to parse the document and execute deferred and parser-inserted scripts (DOMContentLoaded), including the network time from the user's location to your server. Parsing of the document is finished, the Document Object Model is ready, but referenced style sheets, images, and subframes may not be finished loading. This event is often the starting point for javascript framework execution, e.g., JQuery's onready() callback, etc."
        }
    },
    "ga:avgDomContentLoadedTime": {
        "id": "ga:avgDomContentLoadedTime",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "Avg. Document Content Loaded Time (sec)",
            "description": "The average time (in seconds) it takes the browser to parse the document.",
            "calculation": "(ga:domContentLoadedTime / ga:domLatencyMetricsSample / 1000)"
        }
    },
    "ga:domLatencyMetricsSample": {
        "id": "ga:domLatencyMetricsSample",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Site Speed",
            "status": "PUBLIC",
            "uiName": "DOM Latency Metrics Sample",
            "description": "The sample set (or count) of pageviews used to calculate the averages for site speed DOM metrics. This metric is used in the avgDomContentLoadedTime and avgDomInteractiveTime calculations."
        }
    },
    "ga:appInstallerId": {
        "id": "ga:appInstallerId",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "App Installer ID",
            "description": "ID of the installer (e.g., Google Play Store) from which the app was downloaded. By default, the app installer id is set based on the PackageManager#getInstallerPackageName method.",
            "allowedInSegments": "true"
        }
    },
    "ga:appVersion": {
        "id": "ga:appVersion",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "App Version",
            "description": "The version of the application.",
            "allowedInSegments": "true"
        }
    },
    "ga:appName": {
        "id": "ga:appName",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "App Name",
            "description": "The name of the application.",
            "allowedInSegments": "true"
        }
    },
    "ga:appId": {
        "id": "ga:appId",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "App ID",
            "description": "The ID of the application.",
            "allowedInSegments": "true"
        }
    },
    "ga:screenName": {
        "id": "ga:screenName",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "Screen Name",
            "description": "The name of the screen.",
            "allowedInSegments": "true"
        }
    },
    "ga:screenDepth": {
        "id": "ga:screenDepth",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "Screen Depth",
            "description": "The number of screenviews per session reported as a string. Can be useful for historgrams.",
            "allowedInSegments": "true"
        }
    },
    "ga:landingScreenName": {
        "id": "ga:landingScreenName",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "Landing Screen",
            "description": "The name of the first screen viewed.",
            "allowedInSegments": "true"
        }
    },
    "ga:exitScreenName": {
        "id": "ga:exitScreenName",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "Exit Screen",
            "description": "The name of the screen when the user exited the application.",
            "allowedInSegments": "true"
        }
    },
    "ga:screenviews": {
        "id": "ga:screenviews",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "Screen Views",
            "description": "The total number of screenviews.",
            "allowedInSegments": "true"
        }
    },
    "ga:appviews": {
        "id": "ga:appviews",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:screenviews",
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "App Tracking",
            "status": "DEPRECATED",
            "uiName": "Screen Views",
            "description": "The total number of screenviews.",
            "allowedInSegments": "true"
        }
    },
    "ga:uniqueScreenviews": {
        "id": "ga:uniqueScreenviews",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "Unique Screen Views",
            "description": "The number of different (unique) screenviews within a session.",
            "allowedInSegments": "true"
        }
    },
    "ga:uniqueAppviews": {
        "id": "ga:uniqueAppviews",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:uniqueScreenviews",
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "App Tracking",
            "status": "DEPRECATED",
            "uiName": "Unique Screen Views",
            "description": "The number of different (unique) screenviews within a session.",
            "allowedInSegments": "true"
        }
    },
    "ga:screenviewsPerSession": {
        "id": "ga:screenviewsPerSession",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "Screens / Session",
            "description": "The average number of screenviews per session.",
            "calculation": "ga:screenviews / ga:sessions"
        }
    },
    "ga:appviewsPerVisit": {
        "id": "ga:appviewsPerVisit",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:screenviewsPerSession",
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "App Tracking",
            "status": "DEPRECATED",
            "uiName": "Screens / Session",
            "description": "The average number of screenviews per session.",
            "calculation": "ga:screenviews / ga:sessions"
        }
    },
    "ga:timeOnScreen": {
        "id": "ga:timeOnScreen",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "TIME",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "Time on Screen",
            "description": "The time spent viewing the current screen.",
            "allowedInSegments": "true"
        }
    },
    "ga:avgScreenviewDuration": {
        "id": "ga:avgScreenviewDuration",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "TIME",
            "group": "App Tracking",
            "status": "PUBLIC",
            "uiName": "Avg. Time on Screen",
            "description": "The average amount of time users spent on a screen in seconds."
        }
    },
    "ga:eventCategory": {
        "id": "ga:eventCategory",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Event Tracking",
            "status": "PUBLIC",
            "uiName": "Event Category",
            "description": "The category of the event.",
            "allowedInSegments": "true"
        }
    },
    "ga:eventAction": {
        "id": "ga:eventAction",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Event Tracking",
            "status": "PUBLIC",
            "uiName": "Event Action",
            "description": "The action of the event.",
            "allowedInSegments": "true"
        }
    },
    "ga:eventLabel": {
        "id": "ga:eventLabel",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Event Tracking",
            "status": "PUBLIC",
            "uiName": "Event Label",
            "description": "The label of the event.",
            "allowedInSegments": "true"
        }
    },
    "ga:totalEvents": {
        "id": "ga:totalEvents",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Event Tracking",
            "status": "PUBLIC",
            "uiName": "Total Events",
            "description": "The total number of events for the profile, across all categories.",
            "allowedInSegments": "true"
        }
    },
    "ga:uniqueEvents": {
        "id": "ga:uniqueEvents",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Event Tracking",
            "status": "PUBLIC",
            "uiName": "Unique Events",
            "description": "The total number of unique events for the profile, across all categories."
        }
    },
    "ga:eventValue": {
        "id": "ga:eventValue",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Event Tracking",
            "status": "PUBLIC",
            "uiName": "Event Value",
            "description": "The total value of events for the profile.",
            "allowedInSegments": "true"
        }
    },
    "ga:avgEventValue": {
        "id": "ga:avgEventValue",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Event Tracking",
            "status": "PUBLIC",
            "uiName": "Avg. Value",
            "description": "The average value of an event.",
            "calculation": "ga:eventValue / ga:totalEvents"
        }
    },
    "ga:sessionsWithEvent": {
        "id": "ga:sessionsWithEvent",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Event Tracking",
            "status": "PUBLIC",
            "uiName": "Sessions with Event",
            "description": "The total number of sessions with events.",
            "allowedInSegments": "true"
        }
    },
    "ga:visitsWithEvent": {
        "id": "ga:visitsWithEvent",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:sessionsWithEvent",
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Event Tracking",
            "status": "DEPRECATED",
            "uiName": "Sessions with Event",
            "description": "The total number of sessions with events.",
            "allowedInSegments": "true"
        }
    },
    "ga:eventsPerSessionWithEvent": {
        "id": "ga:eventsPerSessionWithEvent",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Event Tracking",
            "status": "PUBLIC",
            "uiName": "Events / Session",
            "description": "The average number of events per session with event.",
            "calculation": "ga:totalEvents / ga:sessionsWithEvent"
        }
    },
    "ga:eventsPerVisitWithEvent": {
        "id": "ga:eventsPerVisitWithEvent",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:eventsPerSessionWithEvent",
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Event Tracking",
            "status": "DEPRECATED",
            "uiName": "Events / Session",
            "description": "The average number of events per session with event.",
            "calculation": "ga:totalEvents / ga:sessionsWithEvent"
        }
    },
    "ga:transactionId": {
        "id": "ga:transactionId",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Transaction",
            "description": "The transaction ID for the shopping cart purchase as supplied by your ecommerce tracking method."
        }
    },
    "ga:affiliation": {
        "id": "ga:affiliation",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Affiliation",
            "description": "Typically used to designate a supplying company or brick and mortar location; product affiliation.",
            "allowedInSegments": "true"
        }
    },
    "ga:sessionsToTransaction": {
        "id": "ga:sessionsToTransaction",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Sessions to Transaction",
            "description": "The number of sessions between users' purchases and the related campaigns that lead to the purchases.",
            "allowedInSegments": "true"
        }
    },
    "ga:visitsToTransaction": {
        "id": "ga:visitsToTransaction",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:sessionsToTransaction",
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Ecommerce",
            "status": "DEPRECATED",
            "uiName": "Sessions to Transaction",
            "description": "The number of sessions between users' purchases and the related campaigns that lead to the purchases.",
            "allowedInSegments": "true"
        }
    },
    "ga:daysToTransaction": {
        "id": "ga:daysToTransaction",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Days to Transaction",
            "description": "The number of days between users' purchases and the related campaigns that lead to the purchases.",
            "allowedInSegments": "true"
        }
    },
    "ga:productSku": {
        "id": "ga:productSku",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Product SKU",
            "description": "The product sku for purchased items as you have defined them in your ecommerce tracking application.",
            "allowedInSegments": "true"
        }
    },
    "ga:productName": {
        "id": "ga:productName",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Product",
            "description": "The product name for purchased items as supplied by your ecommerce tracking application.",
            "allowedInSegments": "true"
        }
    },
    "ga:productCategory": {
        "id": "ga:productCategory",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Product Category",
            "description": "Any product variations (size, color) for purchased items as supplied by your ecommerce application.",
            "allowedInSegments": "true"
        }
    },
    "ga:currencyCode": {
        "id": "ga:currencyCode",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Currency Code",
            "description": "The local currency code of the transaction based on ISO 4217 standard."
        }
    },
    "ga:transactions": {
        "id": "ga:transactions",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Transactions",
            "description": "The total number of transactions.",
            "allowedInSegments": "true"
        }
    },
    "ga:transactionsPerSession": {
        "id": "ga:transactionsPerSession",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Ecommerce Conversion Rate",
            "description": "The average number of transactions for a session on your property.",
            "calculation": "ga:transactions / ga:sessions"
        }
    },
    "ga:transactionsPerVisit": {
        "id": "ga:transactionsPerVisit",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:transactionsPerSession",
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Ecommerce",
            "status": "DEPRECATED",
            "uiName": "Ecommerce Conversion Rate",
            "description": "The average number of transactions for a session on your property.",
            "calculation": "ga:transactions / ga:sessions"
        }
    },
    "ga:transactionRevenue": {
        "id": "ga:transactionRevenue",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Revenue",
            "description": "The total sale revenue provided in the transaction excluding shipping and tax.",
            "allowedInSegments": "true"
        }
    },
    "ga:revenuePerTransaction": {
        "id": "ga:revenuePerTransaction",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Average Order Value",
            "description": "The average revenue for an e-commerce transaction.",
            "calculation": "ga:transactionRevenue / ga:transactions"
        }
    },
    "ga:transactionRevenuePerSession": {
        "id": "ga:transactionRevenuePerSession",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Per Session Value",
            "description": "Average transaction revenue for a session on your property.",
            "calculation": "ga:transactionRevenue / ga:sessions"
        }
    },
    "ga:transactionRevenuePerVisit": {
        "id": "ga:transactionRevenuePerVisit",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:transactionRevenuePerSession",
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "DEPRECATED",
            "uiName": "Per Session Value",
            "description": "Average transaction revenue for a session on your property.",
            "calculation": "ga:transactionRevenue / ga:sessions"
        }
    },
    "ga:transactionShipping": {
        "id": "ga:transactionShipping",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Shipping",
            "description": "The total cost of shipping.",
            "allowedInSegments": "true"
        }
    },
    "ga:transactionTax": {
        "id": "ga:transactionTax",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Tax",
            "description": "The total amount of tax.",
            "allowedInSegments": "true"
        }
    },
    "ga:totalValue": {
        "id": "ga:totalValue",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Total Value",
            "description": "Total value for your property (including total revenue and total goal value).",
            "calculation": "(ga:transactionRevenue + ga:goalValueAll)"
        }
    },
    "ga:itemQuantity": {
        "id": "ga:itemQuantity",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Quantity",
            "description": "The total number of items purchased. For example, if users purchase 2 frisbees and 5 tennis balls, 7 items have been purchased.",
            "allowedInSegments": "true"
        }
    },
    "ga:uniquePurchases": {
        "id": "ga:uniquePurchases",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Unique Purchases",
            "description": "The number of product sets purchased. For example, if users purchase 2 frisbees and 5 tennis balls from your site, 2 unique products have been purchased.",
            "allowedInSegments": "true"
        }
    },
    "ga:revenuePerItem": {
        "id": "ga:revenuePerItem",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Average Price",
            "description": "The average revenue per item.",
            "calculation": "ga:itemRevenue / ga:itemQuantity"
        }
    },
    "ga:itemRevenue": {
        "id": "ga:itemRevenue",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Product Revenue",
            "description": "The total revenue from purchased product items on your property.",
            "allowedInSegments": "true"
        }
    },
    "ga:itemsPerPurchase": {
        "id": "ga:itemsPerPurchase",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Average QTY",
            "description": "The average quantity of this item (or group of items) sold per purchase.",
            "calculation": "ga:itemQuantity / ga:uniquePurchases"
        }
    },
    "ga:localTransactionRevenue": {
        "id": "ga:localTransactionRevenue",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Local Revenue",
            "description": "Transaction revenue in local currency."
        }
    },
    "ga:localTransactionShipping": {
        "id": "ga:localTransactionShipping",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Local Shipping",
            "description": "Transaction shipping cost in local currency."
        }
    },
    "ga:localTransactionTax": {
        "id": "ga:localTransactionTax",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Local Tax",
            "description": "Transaction tax in local currency."
        }
    },
    "ga:localItemRevenue": {
        "id": "ga:localItemRevenue",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Ecommerce",
            "status": "PUBLIC",
            "uiName": "Local Product Revenue",
            "description": "Product revenue in local currency.",
            "allowedInSegments": "true"
        }
    },
    "ga:socialInteractionNetwork": {
        "id": "ga:socialInteractionNetwork",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Interactions",
            "status": "PUBLIC",
            "uiName": "Social Source",
            "description": "For social interactions, a value representing the social network being tracked."
        }
    },
    "ga:socialInteractionAction": {
        "id": "ga:socialInteractionAction",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Interactions",
            "status": "PUBLIC",
            "uiName": "Social Action",
            "description": "For social interactions, a value representing the social action being tracked (e.g. +1, bookmark)"
        }
    },
    "ga:socialInteractionNetworkAction": {
        "id": "ga:socialInteractionNetworkAction",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Interactions",
            "status": "PUBLIC",
            "uiName": "Social Source and Action",
            "description": "For social interactions, a value representing the concatenation of the socialInteractionNetwork and socialInteractionAction action being tracked at this hit level (e.g. Google: +1)"
        }
    },
    "ga:socialInteractionTarget": {
        "id": "ga:socialInteractionTarget",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Interactions",
            "status": "PUBLIC",
            "uiName": "Social Entity",
            "description": "For social interactions, a value representing the URL (or resource) which receives the social network action."
        }
    },
    "ga:socialEngagementType": {
        "id": "ga:socialEngagementType",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Social Interactions",
            "status": "PUBLIC",
            "uiName": "Social Type",
            "description": "Engagement type. Possible values are \"Socially Engaged\" or \"Not Socially Engaged\"."
        }
    },
    "ga:socialInteractions": {
        "id": "ga:socialInteractions",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Social Interactions",
            "status": "PUBLIC",
            "uiName": "Social Actions",
            "description": "The total number of social interactions on your property."
        }
    },
    "ga:uniqueSocialInteractions": {
        "id": "ga:uniqueSocialInteractions",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Social Interactions",
            "status": "PUBLIC",
            "uiName": "Unique Social Actions",
            "description": "The number of sessions during which the specified social action(s) occurred at least once. This is based on the the unique combination of socialInteractionNetwork, socialInteractionAction, and socialInteractionTarget."
        }
    },
    "ga:socialInteractionsPerSession": {
        "id": "ga:socialInteractionsPerSession",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Social Interactions",
            "status": "PUBLIC",
            "uiName": "Actions Per Social Session",
            "description": "The number of social interactions per session on your property.",
            "calculation": "ga:socialInteractions / ga:uniqueSocialInteractions"
        }
    },
    "ga:socialInteractionsPerVisit": {
        "id": "ga:socialInteractionsPerVisit",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:socialInteractionsPerSession",
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "Social Interactions",
            "status": "DEPRECATED",
            "uiName": "Actions Per Social Session",
            "description": "The number of social interactions per session on your property.",
            "calculation": "ga:socialInteractions / ga:uniqueSocialInteractions"
        }
    },
    "ga:userTimingCategory": {
        "id": "ga:userTimingCategory",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "User Timings",
            "status": "PUBLIC",
            "uiName": "Timing Category",
            "description": "A string for categorizing all user timing variables into logical groups for easier reporting purposes.",
            "allowedInSegments": "true"
        }
    },
    "ga:userTimingLabel": {
        "id": "ga:userTimingLabel",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "User Timings",
            "status": "PUBLIC",
            "uiName": "Timing Label",
            "description": "The name of the resource's action being tracked.",
            "allowedInSegments": "true"
        }
    },
    "ga:userTimingVariable": {
        "id": "ga:userTimingVariable",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "User Timings",
            "status": "PUBLIC",
            "uiName": "Timing Variable",
            "description": "A value that can be used to add flexibility in visualizing user timings in the reports.",
            "allowedInSegments": "true"
        }
    },
    "ga:userTimingValue": {
        "id": "ga:userTimingValue",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "User Timings",
            "status": "PUBLIC",
            "uiName": "User Timing (ms)",
            "description": "The total number of milliseconds for a user timing."
        }
    },
    "ga:userTimingSample": {
        "id": "ga:userTimingSample",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "User Timings",
            "status": "PUBLIC",
            "uiName": "User Timing Sample",
            "description": "The number of hits that were sent for a particular userTimingCategory, userTimingLabel, and userTimingVariable."
        }
    },
    "ga:avgUserTimingValue": {
        "id": "ga:avgUserTimingValue",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "FLOAT",
            "group": "User Timings",
            "status": "PUBLIC",
            "uiName": "Avg. User Timing (sec)",
            "description": "The average amount of elapsed time.",
            "calculation": "(ga:userTimingValue / ga:userTimingSample / 1000)"
        }
    },
    "ga:exceptionDescription": {
        "id": "ga:exceptionDescription",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Exceptions",
            "status": "PUBLIC",
            "uiName": "Exception Description",
            "description": "The description for the exception.",
            "allowedInSegments": "true"
        }
    },
    "ga:exceptions": {
        "id": "ga:exceptions",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Exceptions",
            "status": "PUBLIC",
            "uiName": "Exceptions",
            "description": "The number of exceptions that were sent to Google Analytics.",
            "allowedInSegments": "true"
        }
    },
    "ga:exceptionsPerScreenview": {
        "id": "ga:exceptionsPerScreenview",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Exceptions",
            "status": "PUBLIC",
            "uiName": "Exceptions / Screen",
            "description": "The number of exceptions thrown divided by the number of screenviews.",
            "calculation": "ga:exceptions / ga:screenviews"
        }
    },
    "ga:fatalExceptions": {
        "id": "ga:fatalExceptions",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Exceptions",
            "status": "PUBLIC",
            "uiName": "Crashes",
            "description": "The number of exceptions where isFatal is set to true.",
            "allowedInSegments": "true"
        }
    },
    "ga:fatalExceptionsPerScreenview": {
        "id": "ga:fatalExceptionsPerScreenview",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Exceptions",
            "status": "PUBLIC",
            "uiName": "Crashes / Screen",
            "description": "The number of fatal exceptions thrown divided by the number of screenviews.",
            "calculation": "ga:fatalExceptions / ga:screenviews"
        }
    },
    "ga:experimentId": {
        "id": "ga:experimentId",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Content Experiments",
            "status": "PUBLIC",
            "uiName": "Experiment ID",
            "description": "The user-scoped id of the content experiment that the user was exposed to when the metrics were reported.",
            "allowedInSegments": "true"
        }
    },
    "ga:experimentVariant": {
        "id": "ga:experimentVariant",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Content Experiments",
            "status": "PUBLIC",
            "uiName": "Variation",
            "description": "The user-scoped id of the particular variation that the user was exposed to during a content experiment.",
            "allowedInSegments": "true"
        }
    },
    "ga:dimensionXX": {
        "id": "ga:dimensionXX",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Custom Variables or Columns",
            "status": "PUBLIC",
            "uiName": "Custom Dimension ",
            "description": "The name of the requested custom dimension, where XX refers the number/index of the custom dimension.",
            "minTemplateIndex": "1",
            "maxTemplateIndex": "20",
            "premiumMinTemplateIndex": "1",
            "premiumMaxTemplateIndex": "200",
            "allowedInSegments": "true"
        }
    },
    "ga:customVarNameXX": {
        "id": "ga:customVarNameXX",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Custom Variables or Columns",
            "status": "PUBLIC",
            "uiName": "Custom Variable (Key 1)",
            "description": "The name for the requested custom variable.",
            "minTemplateIndex": "1",
            "maxTemplateIndex": "5",
            "premiumMinTemplateIndex": "1",
            "premiumMaxTemplateIndex": "50",
            "allowedInSegments": "true"
        }
    },
    "ga:metricXX": {
        "id": "ga:metricXX",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Custom Variables or Columns",
            "status": "PUBLIC",
            "uiName": "Custom Metric Value",
            "description": "The name of the requested custom metric, where XX refers the number/index of the custom metric.",
            "minTemplateIndex": "1",
            "maxTemplateIndex": "20",
            "premiumMinTemplateIndex": "1",
            "premiumMaxTemplateIndex": "200",
            "allowedInSegments": "true"
        }
    },
    "ga:customVarValueXX": {
        "id": "ga:customVarValueXX",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Custom Variables or Columns",
            "status": "PUBLIC",
            "uiName": "Custom Variable (Value 01)",
            "description": "The value for the requested custom variable.",
            "minTemplateIndex": "1",
            "maxTemplateIndex": "5",
            "premiumMinTemplateIndex": "1",
            "premiumMaxTemplateIndex": "50",
            "allowedInSegments": "true"
        }
    },
    "ga:date": {
        "id": "ga:date",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Date",
            "description": "The date of the session formatted as YYYYMMDD."
        }
    },
    "ga:year": {
        "id": "ga:year",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Year",
            "description": "The year of the session. A four-digit year from 2005 to the current year."
        }
    },
    "ga:month": {
        "id": "ga:month",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Month of the year",
            "description": "The month of the session. A two digit integer from 01 to 12."
        }
    },
    "ga:week": {
        "id": "ga:week",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Week of the Year",
            "description": "The week of the session. A two-digit number from 01 to 53. Each week starts on Sunday."
        }
    },
    "ga:day": {
        "id": "ga:day",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Day of the month",
            "description": "The day of the month. A two-digit number from 01 to 31."
        }
    },
    "ga:hour": {
        "id": "ga:hour",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Hour",
            "description": "A two-digit hour of the day ranging from 00-23 in the timezone configured for the account. This value is also corrected for daylight savings time, adhering to all local rules for daylight savings time. If your timezone follows daylight savings time, there will be an apparent bump in the number of sessions during the change-over hour (e.g. between 1:00 and 2:00) for the day per year when that hour repeats. A corresponding hour with zero sessions will occur at the opposite changeover. (Google Analytics does not track user time more precisely than hours.)",
            "allowedInSegments": "true"
        }
    },
    "ga:minute": {
        "id": "ga:minute",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Minute",
            "description": "Returns the minute in the hour. The possible values are between 00 and 59.",
            "allowedInSegments": "true"
        }
    },
    "ga:nthMonth": {
        "id": "ga:nthMonth",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Month Index",
            "description": "Index for each month in the specified date range. Index for the first month in the date range is 0, 1 for the second month, and so on. The index corresponds to month entries."
        }
    },
    "ga:nthWeek": {
        "id": "ga:nthWeek",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Week Index",
            "description": "Index for each week in the specified date range. Index for the first week in the date range is 0, 1 for the second week, and so on. The index corresponds to week entries."
        }
    },
    "ga:nthDay": {
        "id": "ga:nthDay",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Day Index",
            "description": "Index for each day in the specified date range. Index for the first day (i.e., start-date) in the date range is 0, 1 for the second day, and so on."
        }
    },
    "ga:nthMinute": {
        "id": "ga:nthMinute",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Minute Index",
            "description": "Index for each minute in the specified date range. Index for the first minute of first day (i.e., start-date) in the date range is 0, 1 for the next minute, and so on."
        }
    },
    "ga:dayOfWeek": {
        "id": "ga:dayOfWeek",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Day of Week",
            "description": "The day of the week. A one-digit number from 0 (Sunday) to 6 (Saturday)."
        }
    },
    "ga:dayOfWeekName": {
        "id": "ga:dayOfWeekName",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Day of Week Name",
            "description": "The name of the day of the week (in English)."
        }
    },
    "ga:dateHour": {
        "id": "ga:dateHour",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Hour of Day",
            "description": "Combined values of ga:date and ga:hour."
        }
    },
    "ga:yearMonth": {
        "id": "ga:yearMonth",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Month of Year",
            "description": "Combined values of ga:year and ga:month."
        }
    },
    "ga:yearWeek": {
        "id": "ga:yearWeek",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Week of Year",
            "description": "Combined values of ga:year and ga:week."
        }
    },
    "ga:isoWeek": {
        "id": "ga:isoWeek",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "ISO Week of the Year",
            "description": "The ISO week number, where each week starts with a Monday. ga:isoWeek should only be used with ga:isoYear since ga:year represents gregorian calendar."
        }
    },
    "ga:isoYear": {
        "id": "ga:isoYear",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "ISO Year",
            "description": "The ISO year of the session. ga:isoYear should only be used with ga:isoWeek since ga:week represents gregorian calendar."
        }
    },
    "ga:isoYearIsoWeek": {
        "id": "ga:isoYearIsoWeek",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "ISO Week of ISO Year",
            "description": "Combined values of ga:isoYear and ga:isoWeek."
        }
    },
    "ga:userAgeBracket": {
        "id": "ga:userAgeBracket",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Audience",
            "status": "PUBLIC",
            "uiName": "Age",
            "description": "Age bracket of user.",
            "allowedInSegments": "true"
        }
    },
    "ga:visitorAgeBracket": {
        "id": "ga:visitorAgeBracket",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:userAgeBracket",
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Audience",
            "status": "DEPRECATED",
            "uiName": "Age",
            "description": "Age bracket of user.",
            "allowedInSegments": "true"
        }
    },
    "ga:userGender": {
        "id": "ga:userGender",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Audience",
            "status": "PUBLIC",
            "uiName": "Gender",
            "description": "Gender of user.",
            "allowedInSegments": "true"
        }
    },
    "ga:visitorGender": {
        "id": "ga:visitorGender",
        "kind": "analytics#column",
        "attributes": {
            "replacedBy": "ga:userGender",
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Audience",
            "status": "DEPRECATED",
            "uiName": "Gender",
            "description": "Gender of user.",
            "allowedInSegments": "true"
        }
    },
    "ga:interestOtherCategory": {
        "id": "ga:interestOtherCategory",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Audience",
            "status": "PUBLIC",
            "uiName": "Other Category",
            "description": "Indicates that users are more likely to be interested in learning about the specified category, and more likely to be ready to purchase.",
            "allowedInSegments": "true"
        }
    },
    "ga:interestAffinityCategory": {
        "id": "ga:interestAffinityCategory",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Audience",
            "status": "PUBLIC",
            "uiName": "Affinity Category (reach)",
            "description": "Indicates that users are more likely to be interested in learning about the specified category.",
            "allowedInSegments": "true"
        }
    },
    "ga:interestInMarketCategory": {
        "id": "ga:interestInMarketCategory",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Audience",
            "status": "PUBLIC",
            "uiName": "In-market Segment",
            "description": "Indicates that users are more likely to be ready to purchase products or services in the specified category.",
            "allowedInSegments": "true"
        }
    },
    "ga:adsenseRevenue": {
        "id": "ga:adsenseRevenue",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Adsense",
            "status": "PUBLIC",
            "uiName": "AdSense Revenue",
            "description": "The total revenue from AdSense ads.",
            "allowedInSegments": "true"
        }
    },
    "ga:adsenseAdUnitsViewed": {
        "id": "ga:adsenseAdUnitsViewed",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Adsense",
            "status": "PUBLIC",
            "uiName": "AdSense Ad Units Viewed",
            "description": "The number of AdSense ad units viewed. An Ad unit is a set of ads displayed as a result of one piece of the AdSense ad code.",
            "allowedInSegments": "true"
        }
    },
    "ga:adsenseAdsViewed": {
        "id": "ga:adsenseAdsViewed",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Adsense",
            "status": "PUBLIC",
            "uiName": "AdSense Ads Viewed",
            "description": "The number of AdSense ads viewed. Multiple ads can be displayed within an Ad Unit.",
            "allowedInSegments": "true"
        }
    },
    "ga:adsenseAdsClicks": {
        "id": "ga:adsenseAdsClicks",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Adsense",
            "status": "PUBLIC",
            "uiName": "AdSense Ads Clicked",
            "description": "The number of times AdSense ads on your site were clicked.",
            "allowedInSegments": "true"
        }
    },
    "ga:adsensePageImpressions": {
        "id": "ga:adsensePageImpressions",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Adsense",
            "status": "PUBLIC",
            "uiName": "AdSense Page Impressions",
            "description": "The number of pageviews during which an AdSense ad was displayed. A page impression can have multiple Ad Units.",
            "allowedInSegments": "true"
        }
    },
    "ga:adsenseCTR": {
        "id": "ga:adsenseCTR",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "PERCENT",
            "group": "Adsense",
            "status": "PUBLIC",
            "uiName": "AdSense CTR",
            "description": "The percentage of page impressions that resulted in a click on an AdSense ad.",
            "calculation": "ga:adsenseAdsClicks/ga:adsensePageImpressions"
        }
    },
    "ga:adsenseECPM": {
        "id": "ga:adsenseECPM",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "CURRENCY",
            "group": "Adsense",
            "status": "PUBLIC",
            "uiName": "AdSense eCPM",
            "description": "The estimated cost per thousand page impressions. It is your AdSense Revenue per 1000 page impressions.",
            "calculation": "ga:adsenseRevenue/(ga:adsensePageImpressions/1000)"
        }
    },
    "ga:adsenseExits": {
        "id": "ga:adsenseExits",
        "kind": "analytics#column",
        "attributes": {
            "type": "METRIC",
            "dataType": "INTEGER",
            "group": "Adsense",
            "status": "PUBLIC",
            "uiName": "AdSense Exits",
            "description": "The number of sessions that ended due to a user clicking on an AdSense ad.",
            "allowedInSegments": "true"
        }
    },
    "ga:isTrueViewVideoAd": {
        "id": "ga:isTrueViewVideoAd",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Adwords",
            "status": "PUBLIC",
            "uiName": "TrueView Video Ad",
            "description": "'Yes' or 'No' - Indicates whether the ad is an AdWords TrueView video ad."
        }
    },
    "ga:nthHour": {
        "id": "ga:nthHour",
        "kind": "analytics#column",
        "attributes": {
            "type": "DIMENSION",
            "dataType": "STRING",
            "group": "Time",
            "status": "PUBLIC",
            "uiName": "Hour Index",
            "description": "Index for each hour in the specified date range. Index for the first hour of first day (i.e., start-date) in the date range is 0, 1 for the next hour, and so on."
        }
    }
};

// Function list:
    //date functions:

    function last_30_days() {
        var cur_date = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate());
        var last_date = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate() - 30);
        myObj.dateFrom = last_date.getFullYear()+"-"+(last_date.getMonth()+1)+"-"+last_date.getDate();
        myObj.dateTill = cur_date.getFullYear()+"-"+(cur_date.getMonth()+1)+"-"+cur_date.getDate();
        $("#Last_30_days").val("from "+myObj.dateFrom+" till "+myObj.dateTill );
    }

    function today() {
        var last_date = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate());
        var cur_date = currentDate;
        myObj.dateFrom = last_date.getFullYear()+"-"+(last_date.getMonth()+1)+"-"+last_date.getDate();
        myObj.dateTill = cur_date.getFullYear()+"-"+(cur_date.getMonth()+1)+"-"+cur_date.getDate();
        $("#Today").val("from "+myObj.dateFrom+" till "+myObj.dateTill);
    }

    function yesterday() {
        var last_date = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate() - 1);
        var cur_date = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate());
        myObj.dateFrom = last_date.getFullYear()+"-"+(last_date.getMonth()+1)+"-"+last_date.getDate();
        myObj.dateTill = cur_date.getFullYear()+"-"+(cur_date.getMonth()+1)+"-"+cur_date.getDate();
        $("#Yesterday").val("from "+myObj.dateFrom+" till "+myObj.dateTill);
    }

    function last_week() {
        var cur_date = new Date(currentDate.getFullYear(), currentDate.getMonth(), (currentDate.getDate() - currentDate.getDay()));
        var last_date = new Date(currentDate.getFullYear(), currentDate.getMonth(), (currentDate.getDate() - 7 - currentDate.getDay()));
        myObj.dateFrom = last_date.getFullYear()+"-"+(last_date.getMonth()+1)+"-"+last_date.getDate();
        myObj.dateTill = cur_date.getFullYear()+"-"+(cur_date.getMonth()+1)+"-"+cur_date.getDate();
        $("#Last_Week").val("from "+myObj.dateFrom+" till "+myObj.dateTill);
    }

    function last_month() {
        var cur_date = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        var last_date = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
        myObj.dateFrom = last_date.getFullYear()+"-"+(last_date.getMonth()+1)+"-"+last_date.getDate();
        myObj.dateTill = cur_date.getFullYear()+"-"+(cur_date.getMonth()+1)+"-"+cur_date.getDate();
        $("#Last_Month").val("from "+myObj.dateFrom+" till "+myObj.dateTill);
    }

	function hide_lists(img_class) {
		$("ul").children(".points").hide(200);
		$(img_class).attr("src", "1.png");
	}
	function change_img(points_id, img_id) {
		if ($(points_id).not(":hidden")) {
	    	$(img_id).attr("src", "2.png");
	    } else {	    	
	    	$(img_id).attr("src", "1.png");
	    }
	}
	function wide_settings() {
		$("#wide_settings").click(function () {
			if($("#wide_settings input:checkbox").is(":checked")) {
				$("#wide_settings_block").show(200);
			} else {
				$("#wide_settings_block").hide(200);
			}
		});
	}
	function clear_checkboxes() {
		$(".points input:checkbox").prop("checked", false);
		$(".sh_content").css("text-shadow", "none");
		$(".sh_content").css("color", "#7c7c7c");
	}
	function metrics_turnSiteUsage() {
		clear_checkboxes();
		$("#sh_content_4").css("color", "#000");
		$("#sh_content_4").css("text-shadow", "0 0 12px #127dff");
		$("#points_4").children().children().children("[name = Country]").prop("checked", true);

		$("#sh_content_18").css("color", "#000");
		$("#sh_content_18").css("text-shadow", "0 0 12px #127dff");
		$("#points_18").children().children().children("[name = NewUsers]").prop("checked", true);
		$("#points_18").children().children().children("[name = PercentNewSessions]").prop("checked", true);

		$("#sh_content_19").css("color", "#000");
		$("#sh_content_19").css("text-shadow", "0 0 12px #127dff");
		$("#points_19").children().children().children("[name = Pageviews]").prop("checked", true);

		$("#sh_content_20").css("color", "#000");
		$("#sh_content_20").css("text-shadow", "0 0 12px #127dff");
		$("#points_20").children().children().children("[name = Sessions]").prop("checked", true);

		$("#sh_content_16").css("color", "#000");
		$("#sh_content_16").css("text-shadow", "0 0 12px #127dff");
		$("#points_16").children().children().children("[name = GoalXXConversionRate]").prop("checked", true);
		$("#points_16").children().children().children("[name = GoalXXCompletions]").prop("checked", true);

		$("#sh_content_12").css("color", "#000");
		$("#sh_content_12").css("text-shadow", "0 0 12px #127dff");
		$("#points_12").children().children().children("[name = Date]").prop("checked", true);
	}
	function metrics_turnSimple() {
		clear_checkboxes();
		$("#sh_content_4").css("color", "#000");
		$("#sh_content_4").css("text-shadow", "0 0 12px #127dff");
		$("#points_4").children().children().children("[name = Country]").prop("checked", true);

		$("#sh_content_4").css("color", "#000");
		$("#sh_content_4").css("text-shadow", "0 0 12px #127dff");
		$("#points_4").children().children().children("[name = Region]").prop("checked", true);

		$("#sh_content_12").css("color", "#000");
		$("#sh_content_12").css("text-shadow", "0 0 12px #127dff");
		$("#points_12").children().children().children("[name = Date]").prop("checked", true);
	}
	function metrics_turnEcommerce() {
		clear_checkboxes();
		$("#sh_content_15").css("color", "#000");
		$("#sh_content_15").css("text-shadow", "0 0 12px #127dff");
		$("#points_15").children().children().children("[name = AvgEventValue]").prop("checked", true);

		$("#sh_content_16").css("color", "#000");
		$("#sh_content_16").css("text-shadow", "0 0 12px #127dff");
		$("#points_16").children().children().children("[name = GoalStartsAll]").prop("checked", true);

		$("#sh_content_20").css("color", "#000");
		$("#sh_content_20").css("text-shadow", "0 0 12px #127dff");
		$("#points_20").children().children().children("[name = AvgSessionDuration]").prop("checked", true);
	}

    function metrics_turnTest() {
        clear_checkboxes();
        $("#sh_content_19").css("color", "#000");
        $("#sh_content_19").css("text-shadow", "0 0 12px #127dff");
        $("#points_19").children().children().children("[name = Pageviews]").prop("checked", true);

        $("#sh_content_12").css("color", "#000");
        $("#sh_content_12").css("text-shadow", "0 0 12px #127dff");
        $("#points_12").children().children().children("[name = Date]").prop("checked", true);
    }

    function get_data() {
        // var data = new Object();
            data.prof_id = submit_form();
            data.datefrom = (function () {
                var dateFrom = myObj.dateFrom;
                return dateFrom;
            })();
            data.datetill = (function () {
                var dateTill = myObj.dateTill;
                return dateTill;
            })();
            data.metrics = (function () {
                var arr = new Array();
                var checkboxes_metrics = $("#column_two").children("ul").children().children(".input_check").children().not(".tooltip").children("input");
                for (var i = 0; i < checkboxes_metrics.length; i++) {
                    if (checkboxes_metrics[i].checked) {
                        arr.push(checkboxes_metrics[i].getAttribute("name"));
                    };
                };
                return arr;
            })();
            data.dimensions = (function () {
                var arr = new Array();
                var checkboxes_dimentions = $("#column_one").children("ul").children().children(".input_check").children().not(".tooltip").children("input");
                for (var i = 0; i < checkboxes_dimentions.length; i++) {
                    if (checkboxes_dimentions[i].checked) {
                        arr.push(checkboxes_dimentions[i].getAttribute("name"));
                    };
                };
                return arr;
            })();
            data.token = gapi.auth.getToken().access_token;
            if(data.token !== null) {
                show_waiter();
            }
            data.rancid = (parseInt(gapi.auth.getToken().expires_in) + parseInt(gapi.auth.getToken().issued_at));

            if (data.prof_id == undefined || data.prof_id == "Please select profile...") {
                return false;
            } else {
            // data = JSON.stringify(data);
            // var enc = encodeURI(data);
                return data;
            }
    };

    function AjaxForm(url) {
        if (get_data() == false) {
            alert("Please select your GA profile.");
            hide_waiter();
        } else {
            $.ajax({
                url: url,
                type: "GET",
                dataType: "html",
                data: data,
                success: function(resp) {
                    if (!isNaN(parseInt(resp))) {
                        var capsidea = CI.openSource(resp);
                        document.getElementById('resp').innerHTML = capsidea;
                        $("#resp").fadeIn(200);
                        tid = setTimeout(function() {
                            $("#resp").fadeOut(200);
                            clearTimeout(tid);
                            tid = null;
                        }, 3000);
                    } else {
                        if (resp == 'no data returned from google, please review your selection') {
                            alert("Error: "+resp);
                            console.log(resp);
                        } else {
                            alert("Unknown error!");
                        }
                    }
                    console.log(data);
                    hide_waiter();
                },
                error: function(resp) {
                    document.getElementById('resp').innerHTML = "Error: "+resp;
                    // alert();
                    $("#resp").fadeIn(200);
                    tid = setTimeout(function() {
                        $("#resp").fadeOut(200);
                        clearTimeout(tid);
                        tid = null;
                    }, 3000);
                    console.log(data);
                    hide_waiter();
                }
            });
        }
    }

    function hide_waiter() {
        $(".waiter").hide(200);
    }

    function show_waiter() {
        $(".waiter").show(200);
    }

// After the page is load:

$(document).ready(function() {
    hide_waiter();
	wide_settings();
    last_30_days();

    $("#date_range").change(function () {
    var option_text = $("#date_range option:selected").text();
        switch (option_text) {
            case 'Today':
                today();
            break;
            case 'Yesterday':
                yesterday();
            break;
            case 'Last Week':
                last_week();
            break;
            case 'Last Month':
                last_month();
            break;
        }
    });

	function show_hide_list(elem, points_id, img_id) {
		$(elem).click(function (){
			// change_img(points_id, img_id);
			if($(points_id).is(":hidden")) {
				hide_lists(".image");
				$(points_id).show(200);
				$(img_id).attr("src", "2.png");
			} else {
				$(points_id).hide(200);
				$(img_id).attr("src", "1.png");
			}
		});
	}
	show_hide_list("#sh_content", "#points_1", "#img_1");
	show_hide_list("#sh_content_2", "#points_2", "#img_2");
	show_hide_list("#sh_content_3", "#points_3", "#img_3");
	show_hide_list("#sh_content_4", "#points_4", "#img_4");
	show_hide_list("#sh_content_5", "#points_5", "#img_5");
	show_hide_list("#sh_content_6", "#points_6", "#img_6");
	show_hide_list("#sh_content_7", "#points_7", "#img_7");
	show_hide_list("#sh_content_8", "#points_8", "#img_8");
	show_hide_list("#sh_content_9", "#points_9", "#img_9");
	show_hide_list("#sh_content_10", "#points_10", "#img_10");
	show_hide_list("#sh_content_11", "#points_11", "#img_11");
	show_hide_list("#sh_content_12", "#points_12", "#img_12");
	show_hide_list("#sh_content_13", "#points_13", "#img_13");
	show_hide_list("#sh_content_14", "#points_14", "#img_14");
	show_hide_list("#sh_content_15", "#points_15", "#img_15");
	show_hide_list("#sh_content_16", "#points_16", "#img_16");
	show_hide_list("#sh_content_17", "#points_17", "#img_17");
	show_hide_list("#sh_content_18", "#points_18", "#img_18");
	show_hide_list("#sh_content_19", "#points_19", "#img_19");
	show_hide_list("#sh_content_20", "#points_20", "#img_20");
	show_hide_list("#sh_content_21", "#points_21", "#img_21");
	show_hide_list("#sh_content_22", "#points_22", "#img_22");

	// function show_checked(points_id) {
	// 	$(points_id).change(function() {
	// 		// var ch = points_id+" input:checkbox";
	// 		$("input").filter(":checkbox:checked").each(function () {
	// 			if ($(points_id+" input:checkbox").is(":checked")) {
	// 				$("input").filter(":checkbox:checked").parent().css("color", "#000");
	// 				$("input").filter(":checkbox:checked").parent().css("text-shadow", "0 0 12px #127dff");
	// 			// });
	// 			} else if ($(points_id+" input:checkbox").not(":checked")) {
	// 			// $("input").filter(":checkbox:checked").each(function () {
	// 				$("input").filter(":checkbox:checked").parent().css("color", "#7c7c7c");
	// 				$("input").filter(":checkbox:checked").parent().css("text-shadow", "none");
	// 			}
	// 		});
	// 	});
	// }

    // function is_checked() {
    //     $('#column_one').children("ul").children(".points").children().not(".tooltip").click(function (evt) {
    //         // for (var i = 0; i < $(evt.target).children().children("input").length; i++) {
    //             var checkbox = $(evt.target).children().children("input");
    //             switch (checkbox.checked) {
    //                 case true:
    //                     $(evt.target).prev().css("color", "#000");
    //                     $(evt.target).prev().css("text-shadow", "0 0 12px #127dff");
    //                     break;
    //                 case false:
    //                     $(evt.target).prev().css("color", "#7c7c7c");
    //                     $(evt.target).prev().css("text-shadow", "none");
    //                     break;
    //             }
    //             // break;
    //         // };
    //     });
    // }

    // function is_checked() {

    // }

    function create_divs() {
        $(".points").find("div").not(".tooltip").not(".input_check").wrap($('<div class="input_check"></div>'));
    };
    create_divs();

    function check_check(column_id) {
        $(column_id).children("ul").children(".points").children().not(".tooltip").click(function (evt) {
            // for (var i = 0; i < $("#column_one input").length; i++) {
                // var checkbox = this.firstChild.nextElementSibling;
                var checkbox = $(evt.target).children("input")[0] || $(evt.target);
                checkbox.checked = !checkbox.checked;
                // is_checked(checkbox, evt);
                // switch (checkbox.checked) {
                // case true:
                //     checkbox.checked = false;
                //     break;
                // case false:
                //     checkbox.checked = true;
                //     break;
                // };
                // $('#column_one').children("ul").children("#points_1").children().not(".tooltip").children("input").prop("checked", true);
            // };
        });
    }
    check_check("#column_one");
    check_check("#column_two");
    // is_checked();

	function is_checked(points_id, show_hide_id) {
	  	$(".input_check").click(function () {
	  		if ($(points_id+" input:checkbox").is(":checked")) {
	  			$(show_hide_id).css("color", "#000");
	  			$(show_hide_id).css("text-shadow", "0 0 12px #127dff");
	  		} else if ($(points_id+" input:checkbox").not(":checked")) {
	  			$(show_hide_id).css("color", "#7c7c7c");
	  			$(show_hide_id).css("text-shadow", "none");
	  		}
	  	});
  	}
  	is_checked("#points_1", "#sh_content");
  	is_checked("#points_2", "#sh_content_2");
  	is_checked("#points_3", "#sh_content_3");
  	is_checked("#points_4", "#sh_content_4");
  	is_checked("#points_5", "#sh_content_5");
  	is_checked("#points_6", "#sh_content_6");
  	is_checked("#points_7", "#sh_content_7");
  	is_checked("#points_8", "#sh_content_8");
  	is_checked("#points_9", "#sh_content_9");
  	is_checked("#points_10", "#sh_content_10");
  	is_checked("#points_11", "#sh_content_11");
    is_checked("#points_12", "#sh_content_12");
  	is_checked("#points_13", "#sh_content_13");
  	is_checked("#points_14", "#sh_content_14");
  	is_checked("#points_15", "#sh_content_15");
  	is_checked("#points_16", "#sh_content_16");
  	is_checked("#points_17", "#sh_content_17");
  	is_checked("#points_18", "#sh_content_18");
  	is_checked("#points_19", "#sh_content_19");
  	is_checked("#points_20", "#sh_content_20");
  	is_checked("#points_21", "#sh_content_21");
  	is_checked("#points_22", "#sh_content_22");

	// function limit() {
	// 	$(".points").change(function () {
	// 		if ($(".Adwords").children("input").filter(":checkbox:checked").length > 7) {
	// 			alert("You can't choose over 7 dimentions!");
	// 			$(".points").children("input :checkbox").prop("checked", false);
	// 		};
	// 	});
	// }
    function limit_dimensions_c1 () {
        $("#column_one").find(".input_check").click(function (evt) {
            var checked = $("#column_one").find(".input_check").children().children("input").filter(":checkbox:checked");
            if (checked.length > 7) {
                alert("Sorry, but you can't choose over 7 dimensions!");
                evt.target.childNodes[1].checked = false;
                if ($(evt.target).parents(".points").children().children().children("input:checked").length < 2) {
                    evt.target.parentNode.parentNode.previousElementSibling.style.color = "rgb(124, 124, 124)";
                    evt.target.parentNode.parentNode.previousElementSibling.style.textShadow = "none";
                };
            };
        });
    }
    function limit_dimensions_c2 () {
        $("#column_two").find(".input_check").click(function (evt) {
            var checked = $("#column_two").find(".input_check").children().children("input").filter(":checkbox:checked");
            if (checked.length > 10) {
                alert("Sorry, but you can't choose over 10 metrics!");
                evt.target.childNodes[1].checked = false;
                if ($(evt.target).parents(".points").children().children().children("input:checked").length < 2) {
                    evt.target.parentNode.parentNode.previousElementSibling.style.color = "rgb(124, 124, 124)";
                    evt.target.parentNode.parentNode.previousElementSibling.style.textShadow = "none";
                };
            };
        });
    }

  	$("#metrics_group").change(function () {
	var option_text = $("#metrics_group option:selected").text();
		switch (option_text) {
            case 'Test':
                metrics_turnTest();
            break;
			case 'Site Usage':
				metrics_turnSiteUsage();
			break;
			case 'Simple Google Analitycs':
				metrics_turnSimple();
			break;
			case 'Ecommerce':
				metrics_turnEcommerce();
			break;
		  	case 'Custom':
		  		clear_checkboxes();
		  	break;
	  	}
	});

	limit_dimensions_c1();
    limit_dimensions_c2();

	// function arr(name) {
	// 	for (var i = 0; i < j.length; i++) {
	// 		n[j[i].id] = j[i];
	// 	};
	// 	console.log(n);
	// 	// console.log(n[name].id);
	// 	// console.log(name);
	// 	document.write(JSON.stringify(n));
	// 	return n[name];
	// }

	function add_tooltip_block(block_name) {
		for (var name in n) {	//перебор объектов
			for (var i = 0; i < $("div."+block_name).length + 1; i++) {		//перебор элементов на странице
				var innerText = "ga:"+$("#"+block_name+"_"+i).text().toLowerCase().trim();	//текст внутри элемента
				var name_modified = name.toLowerCase();		//имя объекта
				if (innerText === name_modified) {		//если совпадают, то:
					// console.log(name);	//debug
					$("#"+block_name+"_"+i).after($('<div id="'+name_modified+'" class="tooltip">'+n[name].attributes.description+'</div>'));	//создать эл-т
				};
			};
		};
	};
	$(".my_class_body").ready(function (evt) {
			add_tooltip_block("Adwords");
			add_tooltip_block("Ecommerce");
			add_tooltip_block("EventTracking");
			add_tooltip_block("GeoNetwork");
			add_tooltip_block("GoalConversions");
			add_tooltip_block("InternalSearch");
			add_tooltip_block("User");
			add_tooltip_block("PageTracking");
			add_tooltip_block("Session");
			add_tooltip_block("System");
			add_tooltip_block("TrafficSources");
			add_tooltip_block("Time");

			add_tooltip_block("Adwords_m");
			add_tooltip_block("Ecommerce_m");
			add_tooltip_block("EventTracking_m");
			add_tooltip_block("GoalConversions_m");
			add_tooltip_block("InternalSearch_m");
			add_tooltip_block("User_m");
			add_tooltip_block("PageTracking_m");
			add_tooltip_block("Session_m");
			add_tooltip_block("SiteSpeed");
			add_tooltip_block("TrafficSources_m");
		});

	function tooltip(hover_block) {
		$(hover_block).mouseover(function e(evt) {
			if (ttid) {
				clearTimeout(ttid);
				ttid = null;
			}
            // $(".tooltip").each(function () { 
            //     if (style_display == false) {
        			ttid = setTimeout(function () {
        				$(hover_block).next().show(200);
                        // style_display = false;
        			} ,500);
                // } else if (style_display == true) {
                    // $(hover_block).next().show(200);
                    // style_display = true;
            //     }
            // });
            var node = evt.target;
            if (node.tagName.toUpperCase() == 'INPUT')
                node=node.parentNode;
            var offset = $(node.parentNode).offset();
			$(hover_block).mousemove(function (e) {
				$(hover_block).next().css({left:e.pageX-offset.left+40, top:e.pageY-offset.top});
				$(hover_block).mouseleave(function () {
					$(hover_block).next().hide(200);
					clearTimeout(ttid);
                    // style_display = false;
				});
			});
		});
	}

	function showTooltips(block) {
		for (var i = 1; i < $("div."+block).length+1; i++) {
			// console.log(block);
			tooltip("#"+block+"_"+i);
		};		
	};
	showTooltips("Adwords");
	showTooltips("Ecommerce");
	showTooltips("Ecommerce_m");
	showTooltips("EventTracking");
	showTooltips("GeoNetwork");
	showTooltips("GoalConversions");
	showTooltips("InternalSearch");
	showTooltips("User");
	showTooltips("PageTracking");
	showTooltips("Session");
	showTooltips("System");
	showTooltips("TrafficSources");
	showTooltips("Time");

	showTooltips("Adwords_m");
	showTooltips("Ecommerce_m");
	showTooltips("EventTracking_m");
	showTooltips("GoalConversions_m");
	showTooltips("InternalSearch_m");
	showTooltips("User_m");
	showTooltips("PageTracking_m");
	showTooltips("Session_m");
	showTooltips("SiteSpeed");
	showTooltips("TrafficSources_m");

});