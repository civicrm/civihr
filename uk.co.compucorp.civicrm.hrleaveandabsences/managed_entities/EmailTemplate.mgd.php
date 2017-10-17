<?php

return [
  [
    'name' => 'Email Template: Leave Request',
    'entity' => 'MessageTemplate',
    'params' => [
      'version' => 3,
      'msg_title' => 'CiviHR Leave Request Notification',
      'msg_subject' => 'Leave Request',
      'msg_text' => 'CiviHR Leave RequestLeave Request Type{ts}Status:{/ts}{$leaveStatus}{ts}Staff Member:{/ts}{contact.display_name}{if $leaveRequest->from_date eq $leaveRequest->to_date}{ts}Date:{/ts}{$fromDate|truncate:10:\'\'|crmDate}{$fromDateType}{else}{ts}From Date:{/ts}{$fromDate|truncate:10:\'\'|crmDate}{$fromDateType}{ts}To Date:{/ts}{$toDate|truncate:10:\'\'|crmDate}{$toDateType}{/if}View This Request{if $leaveComments}Request Comments{foreach from=$leaveComments item=value key=label}{$value.commenter}:{$value.created_at|crmDate}{$value.text}{/foreach}{/if}{if $leaveFiles}Other files recorded on this request{foreach from=$leaveFiles item=value key=label}{$value.name}: Added on{$value.upload_date|crmDate}{/foreach}{/if}',
      'msg_html' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

{* Store all date (and optionally time) labels and captions for later re-use *}
{if $calculationUnitName === \'days\'}
  {assign var="dateLabel" value="Date"}
{/if}
{if $calculationUnitName === \'hours\'}
  {assign var="dateLabel" value="Date/Time"}
{/if}
{capture name=\'fromDate\'}
  {if $calculationUnitName === \'days\'}
    <span>{$fromDate|truncate:10:\'\'|crmDate} {$fromDateType}</span>
  {/if}
  {if $calculationUnitName === \'hours\'}
    <span>{$fromDate|truncate:16:\'\'|crmDate}</span>
  {/if}
{/capture}
{capture name=\'toDate\'}
  {if $calculationUnitName === \'days\'}
    <span>{$toDate|truncate:10:\'\'|crmDate} {$toDateType}</span>
  {/if}
  {if $calculationUnitName === \'hours\'}
    <span>{$toDate|truncate:16:\'\'|crmDate}</span>
  {/if}
{/capture}

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <title>CiviHR Leave Request</title>
    <style>{literal}@media only screen {
  html {
    min-height: 100%;
    background: #E8EEF0;
  }
}

@media only screen and (max-width: 596px) {
  .small-text-left {
    text-align: left !important;
  }
}

@media only screen and (max-width: 596px) {
  table.body img {
    width: auto;
    height: auto;
  }

  table.body center {
    min-width: 0 !important;
  }

  table.body .container {
    width: 95% !important;
  }

  table.body .columns {
    height: auto !important;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    padding-left: 16px !important;
    padding-right: 16px !important;
  }

  table.body .columns .columns {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  table.body .collapse .columns {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  th.small-12 {
    display: inline-block !important;
    width: 100% !important;
  }

  .columns th.small-12 {
    display: block !important;
    width: 100% !important;
  }
}

@media only screen and (max-width: 596px) {
  .email-date {
    line-height: normal !important;
  }
}

@media only screen and (max-width: 596px) {
  .request-data-key {
    padding-bottom: 0 !important;
  }
}

@media only screen and (min-width: 597px) {
  .request-data .row .columns {
    padding-bottom: 10px !important;
  }
}{/literal}</style>
  </head>
  <body style="-moz-box-sizing: border-box; -ms-text-size-adjust: 100%; -webkit-box-sizing: border-box; -webkit-text-size-adjust: 100%; Margin: 0; box-sizing: border-box; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; min-width: 100%; padding: 0; text-align: left; width: 100% !important;">
    <span class="preheader" style="color: #E8EEF0; display: none !important; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; mso-hide: all !important; opacity: 0; overflow: hidden; visibility: hidden;"></span>
    <table class="body" style="Margin: 0; background: #E8EEF0; border-collapse: collapse; border-spacing: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; height: 100%; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
      <tr style="padding: 0; text-align: left; vertical-align: top;">
        <td class="center" align="center" valign="top" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
          <center class="email" data-parsed="" style="Margin: 40px 0; margin: 40px 0; min-width: 580px; width: 100%;">
            <!-- header -->
            <table align="center" class="container email-heading float-center" style="Margin: 0 auto; background: transparent; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
              <table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                <th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 298px;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                  <h1 class="email-title" style="Margin: 0; Margin-bottom: 0; border-left: 5px solid #42AFCB; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 500; line-height: 40px; margin: 0; margin-bottom: 0; padding: 0; padding-left: 15px; text-align: left; word-wrap: normal;">CiviHR Leave Request</h1>
                </th></tr></table></th>
                <th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 298px;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                  <p class="email-date small-text-left text-right" style="Margin: 0; Margin-bottom: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 40px; margin: 0; margin-bottom: 0; padding: 0; text-align: right;">{$currentDateTime->format("D d F Y")}</p>
                </th></tr></table></th>
              </tr></tbody></table>
            </td></tr></tbody></table>
            <table class="spacer float-center" style="Margin: 0 auto; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&#xA0;</td></tr></tbody></table>
            <!-- body -->
            <table align="center" class="container float-center" style="Margin: 0 auto; background: transparent; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">

              <div class="request">
                <table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                  <th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 588px;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                    <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                      <tr style="padding: 0; text-align: left; vertical-align: top;">
                        <td class="callout-header" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #F3F6F7; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 15px 20px; text-align: left; vertical-align: top; word-wrap: break-word;">
                          <h2 class="callout-title" style="Margin: 0; Margin-bottom: 0; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 500; line-height: 1.53846; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">{$absenceTypeName}</h2>
                        </td>
                      </tr>
                    </table>

                    <table class="callout" style="Margin-bottom: 0 !important; border-collapse: collapse; border-spacing: 0; margin-bottom: 0 !important; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th class="callout-inner request-data" style="Margin: 0; background: #FFF; border: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 20px; text-align: left; width: 100%;">
                      <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                        <th class="request-data-key small-12 large-3 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 25%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span style="color: #4D5663; font-weight: 600;">
                            {ts}Status:{/ts}
                          </span>
                        </th></tr></table></th>
                        <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span>{$leaveStatus}</span>
                        </th></tr></table></th>
                      </tr></tbody></table>

                      <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                        <th class="request-data-key small-12 large-3 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 25%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span style="color: #4D5663; font-weight: 600;">
                            {ts}Staff Member:{/ts}
                          </span>
                        </th></tr></table></th>
                        <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span>{contact.display_name}</span>
                        </th></tr></table></th>
                      </tr></tbody></table>


                      {if $fromDate eq $toDate}
                        <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="request-data-key small-12 large-3 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 25%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            <span style="color: #4D5663; font-weight: 600;">
                              {ts}{$dateLabel}:{/ts}
                            </span>
                          </th></tr></table></th>
                          <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            {$smarty.capture.fromDate}
                          </th></tr></table></th>
                        </tr></tbody></table>

                      {else}
                        <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="request-data-key small-12 large-3 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 25%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            <span style="color: #4D5663; font-weight: 600;">
                              {ts}From {$dateLabel}:{/ts}
                            </span>
                          </th></tr></table></th>
                          <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            {$smarty.capture.fromDate}
                          </th></tr></table></th>
                        </tr></tbody></table>

                        <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="request-data-key small-12 large-3 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 0 !important; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 25%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            <span style="color: #4D5663; font-weight: 600;">
                              {ts}To {$dateLabel}:{/ts}
                            </span>
                          </th></tr></table></th>
                          <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 0 !important; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            {$smarty.capture.toDate}
                          </th></tr></table></th>
                        </tr></tbody></table>

                      {/if}
                    </th><th class="expander" style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th></tr></table>
                    <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&#xA0;</td></tr></tbody></table>
                    <table class="button expanded alert" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;"><tr style="padding: 0; text-align: left; vertical-align: top;"><td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100%; word-wrap: break-word;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #E6807F; border: 0px solid #E6807F; border-collapse: collapse !important; color: #FFF; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 8px 16px 8px 16px; text-align: left; vertical-align: top; width: 100%; word-wrap: break-word;"><center data-parsed="" style="min-width: 0; width: 100%;"><a href="{$leaveRequestLink}" align="center" class="float-center" style="Margin: 0; border: 0 solid #E6807F; border-radius: 3px; color: #FFF; display: inline-block; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; padding-left: 0; padding-right: 0; text-align: center; text-decoration: none; width: 100%;">View This Request</a></center></td></tr></table></td>
<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td></tr></table>
                    {if $leaveComments}
                      <hr style="Margin: 20px auto; border: 0; border-top: 1px solid #D3DEE2; height: 0; margin: 20px auto;">
                      <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <td class="callout-header" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #F3F6F7; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 15px 20px; text-align: left; vertical-align: top; word-wrap: break-word;">
                            <h2 class="callout-title" style="Margin: 0; Margin-bottom: 0; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 500; line-height: 1.53846; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">Request comments</h2>
                          </td>
                        </tr>
                      </table>

                      <table class="callout" style="Margin-bottom: 0 !important; border-collapse: collapse; border-spacing: 0; margin-bottom: 0 !important; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th class="callout-inner callout-no-padding" style="Margin: 0; background: #FFF; border: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left; width: 100%;">
                        <div class="request-comments">
                          {foreach from=$leaveComments item=value key=label}
                            <table class="request-comment" style="border-bottom: 1px solid #E8EEF0; border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                              <tbody>
                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                  <td class="request-comment-header" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: 600; hyphens: auto; line-height: 1.53846; margin: 0; padding: 20px; padding-bottom: 10px; text-align: left; vertical-align: top; word-wrap: break-word;">
                                    <span class="request-comment-author">{$value.commenter}:</span>
                                    <span class="request-comment-datetime">{$value.created_at|crmDate}</span>
                                  </td>
                                </tr>
                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                  <td class="request-comment-body" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 20px; padding-top: 0; text-align: left; vertical-align: top; word-wrap: break-word;">{$value.text}</td>
                                </tr>
                              </tbody>
                            </table>

                          {/foreach}
                        </div>
                      </th><th class="expander" style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th></tr></table>
                    {/if}
                    {if $leaveFiles}
                      <hr style="Margin: 20px auto; border: 0; border-top: 1px solid #D3DEE2; height: 0; margin: 20px auto;">
                      <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <td class="callout-header" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #F3F6F7; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 15px 20px; text-align: left; vertical-align: top; word-wrap: break-word;">
                            <h2 class="callout-title" style="Margin: 0; Margin-bottom: 0; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 500; line-height: 1.53846; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">Other files recorded in this request</h2>
                          </td>
                        </tr>
                      </table>

                      <table class="callout" style="Margin-bottom: 0 !important; border-collapse: collapse; border-spacing: 0; margin-bottom: 0 !important; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th class="callout-inner" style="Margin: 0; background: #FFF; border: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 20px; text-align: left; width: 100%;">
                        <div class="request-attachments">
                          {foreach from=$leaveFiles item=value key=label}
                            <div class="request-attachment" style="Margin-bottom: 10px; margin-bottom: 10px;">
                              <span class="request-attachment-name" style="color: #4D5663; font-weight: 600;">{$value.name}</span>: Added on {$value.created_at|crmDate}
                            </div>

                          {/foreach}
                        </div>
                      </th><th class="expander" style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th></tr></table>
                    {/if}
                  </th></tr></table></th>
                </tr></tbody></table>
                <table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                  <th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 588px;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                    <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&#xA0;</td></tr></tbody></table>
                    <img class="text-center email-logo" src="https://civihr.org/sites/default/files/email-logo.png" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: 100px;">
                  </th>
<th class="expander" style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th></tr></table></th>
                </tr></tbody></table>

              </div>

            </td></tr></tbody></table>
          </center>
        </td>
      </tr>
    </table>
    <!-- prevent Gmail on iOS font size manipulation -->
   <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
  </body>
</html>',
      'is_reserved' => 1
    ],
  ],
  [
    'name' => 'Email Template: TOIL Request',
    'entity' => 'MessageTemplate',
    'params' => [
      'version' => 3,
      'msg_title' => 'CiviHR TOIL Request Notification',
      'msg_subject' => 'TOIL Request',
      'msg_text' => 'CiviHR TOIL RequestLeave Request Type{ts}Status:{/ts}{$leaveStatus}{ts}Staff Member:{/ts}{contact.display_name}{if $leaveRequest->from_date eq $leaveRequest->to_date}{ts}Date:{/ts}{$fromDate|truncate:10:\'\'|crmDate}{else}{ts}From Date:{/ts}{$fromDate|truncate:10:\'\'|crmDate}{ts}To Date:{/ts}{$toDate|truncate:10:\'\'|crmDate}{/if}{ts}No. TOIL Days Requested{/ts}{$leaveRequest->toil_to_accrue}{if $leaveRequest->toil_to_accrue > 1}days{else}day{/if}View This Request{if $leaveComments}Request Comments{foreach from=$leaveComments item=value key=label}{$value.commenter}:{$value.created_at|crmDate}{$value.text}{/foreach}{/if}{if $leaveFiles}Other files recorded on this request{foreach from=$leaveFiles item=value key=label}{$value.name}: Added on{$value.upload_date|crmDate}{/foreach}{/if}',
      'msg_html' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <title>CiviHR TOIL Request</title>
    <style>{literal}@media only screen {
  html {
    min-height: 100%;
    background: #E8EEF0;
  }
}

@media only screen and (max-width: 596px) {
  .small-text-left {
    text-align: left !important;
  }
}

@media only screen and (max-width: 596px) {
  table.body img {
    width: auto;
    height: auto;
  }

  table.body center {
    min-width: 0 !important;
  }

  table.body .container {
    width: 95% !important;
  }

  table.body .columns {
    height: auto !important;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    padding-left: 16px !important;
    padding-right: 16px !important;
  }

  table.body .columns .columns {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  table.body .collapse .columns {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  th.small-12 {
    display: inline-block !important;
    width: 100% !important;
  }

  .columns th.small-12 {
    display: block !important;
    width: 100% !important;
  }
}

@media only screen and (max-width: 596px) {
  .email-date {
    line-height: normal !important;
  }
}

@media only screen and (max-width: 596px) {
  .request-data-key {
    padding-bottom: 0 !important;
  }
}

@media only screen and (min-width: 597px) {
  .request-data .row .columns {
    padding-bottom: 10px !important;
  }
}{/literal}</style>
  </head>
  <body style="-moz-box-sizing: border-box; -ms-text-size-adjust: 100%; -webkit-box-sizing: border-box; -webkit-text-size-adjust: 100%; Margin: 0; box-sizing: border-box; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; min-width: 100%; padding: 0; text-align: left; width: 100% !important;">
    <span class="preheader" style="color: #E8EEF0; display: none !important; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; mso-hide: all !important; opacity: 0; overflow: hidden; visibility: hidden;"></span>
    <table class="body" style="Margin: 0; background: #E8EEF0; border-collapse: collapse; border-spacing: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; height: 100%; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
      <tr style="padding: 0; text-align: left; vertical-align: top;">
        <td class="center" align="center" valign="top" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
          <center class="email" data-parsed="" style="Margin: 40px 0; margin: 40px 0; min-width: 580px; width: 100%;">
            <!-- header -->
            <table align="center" class="container email-heading float-center" style="Margin: 0 auto; background: transparent; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
              <table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                <th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 298px;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                  <h1 class="email-title" style="Margin: 0; Margin-bottom: 0; border-left: 5px solid #42AFCB; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 500; line-height: 40px; margin: 0; margin-bottom: 0; padding: 0; padding-left: 15px; text-align: left; word-wrap: normal;">CiviHR TOIL Request</h1>
                </th></tr></table></th>
                <th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 298px;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                  <p class="email-date small-text-left text-right" style="Margin: 0; Margin-bottom: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 40px; margin: 0; margin-bottom: 0; padding: 0; text-align: right;">{$currentDateTime->format("D d F Y")}</p>
                </th></tr></table></th>
              </tr></tbody></table>
            </td></tr></tbody></table>
            <table class="spacer float-center" style="Margin: 0 auto; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&#xA0;</td></tr></tbody></table>
            <!-- body -->
            <table align="center" class="container float-center" style="Margin: 0 auto; background: transparent; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">

              <div class="request">
                <table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                  <th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 588px;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                    <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                      <tr style="padding: 0; text-align: left; vertical-align: top;">
                        <td class="callout-header" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #F3F6F7; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 15px 20px; text-align: left; vertical-align: top; word-wrap: break-word;">
                          <h2 class="callout-title" style="Margin: 0; Margin-bottom: 0; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 500; line-height: 1.53846; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">{$absenceTypeName}</h2>
                        </td>
                      </tr>
                    </table>

                    <table class="callout" style="Margin-bottom: 0 !important; border-collapse: collapse; border-spacing: 0; margin-bottom: 0 !important; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th class="callout-inner request-data" style="Margin: 0; background: #FFF; border: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 20px; text-align: left; width: 100%;">
                      <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                        <th class="request-data-key small-12 large-4 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 33.33333%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span style="color: #4D5663; font-weight: 600;">
                            {ts}Status:{/ts}
                          </span>
                        </th></tr></table></th>
                        <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span>{$leaveStatus}</span>
                        </th></tr></table></th>
                      </tr></tbody></table>

                      <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                        <th class="request-data-key small-12 large-4 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 33.33333%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span style="color: #4D5663; font-weight: 600;">
                            {ts}Staff Member:{/ts}
                          </span>
                        </th></tr></table></th>
                        <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span>{contact.display_name}</span>
                        </th></tr></table></th>
                      </tr></tbody></table>


                      {if $leaveRequest->from_date eq $leaveRequest->to_date}
                        <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="request-data-key small-12 large-4 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 33.33333%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            <span style="color: #4D5663; font-weight: 600;">
                              {ts}{$dateLabel}:{/ts}
                            </span>
                          </th></tr></table></th>
                          <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            {$smarty.capture.fromDate}
                          </th></tr></table></th>
                        </tr></tbody></table>

                      {else}
                        <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="request-data-key small-12 large-4 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 33.33333%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            <span style="color: #4D5663; font-weight: 600;">
                              {ts}From {$dateLabel}:{/ts}
                            </span>
                          </th></tr></table></th>
                          <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            {$smarty.capture.fromDate}
                          </th></tr></table></th>
                        </tr></tbody></table>

                        <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="request-data-key small-12 large-4 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 33.33333%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            <span style="color: #4D5663; font-weight: 600;">
                              {ts}To {$dateLabel}:{/ts}
                            </span>
                          </th></tr></table></th>
                          <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            {$smarty.capture.toDate}
                          </th></tr></table></th>
                        </tr></tbody></table>

                      {/if}

                      <table class="row request-data-toil" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                        <th class="request-data-key small-12 large-4 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 0 !important; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 33.33333%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <div style="border: 1px solid #FFF; color: #4D5663; font-weight: 600; padding: 10px 0;">
                            {ts}No. TOIL Days Requested:{/ts}
                          </div>
                        </th></tr></table></th>
                        <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 0 !important; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span style="border: 1px solid #FFF; border-color: #727E8A; display: inline-block; padding: 10px 0; text-align: center; width: 100px;">{$leaveRequest->toil_to_accrue} {if $leaveRequest->toil_to_accrue > 1}days{else}day{/if}</span>
                        </th></tr></table></th>
                      </tr></tbody></table>

                    </th><th class="expander" style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th></tr></table>
                    <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&#xA0;</td></tr></tbody></table>
                    <table class="button expanded alert" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;"><tr style="padding: 0; text-align: left; vertical-align: top;"><td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100%; word-wrap: break-word;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #E6807F; border: 0px solid #E6807F; border-collapse: collapse !important; color: #FFF; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 8px 16px 8px 16px; text-align: left; vertical-align: top; width: 100%; word-wrap: break-word;"><center data-parsed="" style="min-width: 0; width: 100%;"><a href="{$leaveRequestLink}" align="center" class="float-center" style="Margin: 0; border: 0 solid #E6807F; border-radius: 3px; color: #FFF; display: inline-block; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; padding-left: 0; padding-right: 0; text-align: center; text-decoration: none; width: 100%;">View This Request</a></center></td></tr></table></td>
<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td></tr></table>
                    {if $leaveComments}
                      <hr style="Margin: 20px auto; border: 0; border-top: 1px solid #D3DEE2; height: 0; margin: 20px auto;">
                      <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <td class="callout-header" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #F3F6F7; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 15px 20px; text-align: left; vertical-align: top; word-wrap: break-word;">
                            <h2 class="callout-title" style="Margin: 0; Margin-bottom: 0; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 500; line-height: 1.53846; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">Request comments</h2>
                          </td>
                        </tr>
                      </table>

                      <table class="callout" style="Margin-bottom: 0 !important; border-collapse: collapse; border-spacing: 0; margin-bottom: 0 !important; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th class="callout-inner callout-no-padding" style="Margin: 0; background: #FFF; border: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left; width: 100%;">
                        <div class="request-comments">
                          {foreach from=$leaveComments item=value key=label}
                            <table class="request-comment" style="border-bottom: 1px solid #E8EEF0; border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                              <tbody>
                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                  <td class="request-comment-header" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: 600; hyphens: auto; line-height: 1.53846; margin: 0; padding: 20px; padding-bottom: 10px; text-align: left; vertical-align: top; word-wrap: break-word;">
                                    <span class="request-comment-author">{$value.commenter}:</span>
                                    <span class="request-comment-datetime">{$value.created_at|crmDate}</span>
                                  </td>
                                </tr>
                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                  <td class="request-comment-body" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 20px; padding-top: 0; text-align: left; vertical-align: top; word-wrap: break-word;">{$value.text}</td>
                                </tr>
                              </tbody>
                            </table>

                          {/foreach}
                        </div>
                      </th><th class="expander" style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th></tr></table>
                    {/if}
                    {if $leaveFiles}
                      <hr style="Margin: 20px auto; border: 0; border-top: 1px solid #D3DEE2; height: 0; margin: 20px auto;">
                      <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <td class="callout-header" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #F3F6F7; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 15px 20px; text-align: left; vertical-align: top; word-wrap: break-word;">
                            <h2 class="callout-title" style="Margin: 0; Margin-bottom: 0; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 500; line-height: 1.53846; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">Other files recorded in this request</h2>
                          </td>
                        </tr>
                      </table>

                      <table class="callout" style="Margin-bottom: 0 !important; border-collapse: collapse; border-spacing: 0; margin-bottom: 0 !important; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th class="callout-inner" style="Margin: 0; background: #FFF; border: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 20px; text-align: left; width: 100%;">
                        <div class="request-attachments">
                          {foreach from=$leaveFiles item=value key=label}
                            <div class="request-attachment" style="Margin-bottom: 10px; margin-bottom: 10px;">
                              <span class="request-attachment-name" style="color: #4D5663; font-weight: 600;">{$value.name}</span>: Added on {$value.created_at|crmDate}
                            </div>

                          {/foreach}
                        </div>
                      </th><th class="expander" style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th></tr></table>
                    {/if}
                  </th></tr></table></th>
                </tr></tbody></table>
                <table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                  <th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 588px;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                    <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&#xA0;</td></tr></tbody></table>
                    <img class="text-center email-logo" src="https://civihr.org/sites/default/files/email-logo.png" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: 100px;">
                  </th>
<th class="expander" style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th></tr></table></th>
                </tr></tbody></table>

              </div>

            </td></tr></tbody></table>
          </center>
        </td>
      </tr>
    </table>
    <!-- prevent Gmail on iOS font size manipulation -->
   <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
  </body>
</html>',
      'is_reserved' => 1
    ],
  ],
  [
    'name' => 'Email Template: Sickness Request',
    'entity' => 'MessageTemplate',
    'params' => [
      'version' => 3,
      'msg_title' => 'CiviHR Sickness Record Notification',
      'msg_subject' => 'Sickness Request',
      'msg_html' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width">
    <title>CiviHR Sickness Request</title>
    <style>{literal}@media only screen {
  html {
    min-height: 100%;
    background: #E8EEF0;
  }
}

@media only screen and (max-width: 596px) {
  .small-text-left {
    text-align: left !important;
  }
}

@media only screen and (max-width: 596px) {
  table.body img {
    width: auto;
    height: auto;
  }

  table.body center {
    min-width: 0 !important;
  }

  table.body .container {
    width: 95% !important;
  }

  table.body .columns {
    height: auto !important;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    box-sizing: border-box;
    padding-left: 16px !important;
    padding-right: 16px !important;
  }

  table.body .columns .columns {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  table.body .collapse .columns {
    padding-left: 0 !important;
    padding-right: 0 !important;
  }

  th.small-12 {
    display: inline-block !important;
    width: 100% !important;
  }

  .columns th.small-12 {
    display: block !important;
    width: 100% !important;
  }
}

@media only screen and (max-width: 596px) {
  .email-date {
    line-height: normal !important;
  }
}

@media only screen and (max-width: 596px) {
  .request-data-key {
    padding-bottom: 0 !important;
  }
}

@media only screen and (min-width: 597px) {
  .request-data .row .columns {
    padding-bottom: 10px !important;
  }
}{/literal}</style>
  </head>
  <body style="-moz-box-sizing: border-box; -ms-text-size-adjust: 100%; -webkit-box-sizing: border-box; -webkit-text-size-adjust: 100%; Margin: 0; box-sizing: border-box; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; min-width: 100%; padding: 0; text-align: left; width: 100% !important;">
    <span class="preheader" style="color: #E8EEF0; display: none !important; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; mso-hide: all !important; opacity: 0; overflow: hidden; visibility: hidden;"></span>
    <table class="body" style="Margin: 0; background: #E8EEF0; border-collapse: collapse; border-spacing: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; height: 100%; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
      <tr style="padding: 0; text-align: left; vertical-align: top;">
        <td class="center" align="center" valign="top" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
          <center class="email" data-parsed="" style="Margin: 40px 0; margin: 40px 0; min-width: 580px; width: 100%;">
            <!-- header -->
            <table align="center" class="container email-heading float-center" style="Margin: 0 auto; background: transparent; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
              <table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                <th class="small-12 large-6 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 298px;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                  <h1 class="email-title" style="Margin: 0; Margin-bottom: 0; border-left: 5px solid #42AFCB; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 500; line-height: 40px; margin: 0; margin-bottom: 0; padding: 0; padding-left: 15px; text-align: left; word-wrap: normal;">CiviHR Sickness Request</h1>
                </th></tr></table></th>
                <th class="small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 298px;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                  <p class="email-date small-text-left text-right" style="Margin: 0; Margin-bottom: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 40px; margin: 0; margin-bottom: 0; padding: 0; text-align: right;">{$currentDateTime->format("D d F Y")}</p>
                </th></tr></table></th>
              </tr></tbody></table>
            </td></tr></tbody></table>
            <table class="spacer float-center" style="Margin: 0 auto; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&#xA0;</td></tr></tbody></table>
            <!-- body -->
            <table align="center" class="container float-center" style="Margin: 0 auto; background: transparent; border-collapse: collapse; border-spacing: 0; float: none; margin: 0 auto; padding: 0; text-align: center; vertical-align: top; width: 580px;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">

              <div class="request request-sickness">
                <table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                  <th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 588px;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                    <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                      <tr style="padding: 0; text-align: left; vertical-align: top;">
                        <td class="callout-header" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #F3F6F7; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 15px 20px; text-align: left; vertical-align: top; word-wrap: break-word;">
                          <h2 class="callout-title" style="Margin: 0; Margin-bottom: 0; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 500; line-height: 1.53846; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">{$absenceTypeName}</h2>
                        </td>
                      </tr>
                    </table>

                    <table class="callout" style="Margin-bottom: 0 !important; border-collapse: collapse; border-spacing: 0; margin-bottom: 0 !important; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th class="callout-inner request-data" style="Margin: 0; background: #FFF; border: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 20px; text-align: left; width: 100%;">
                      <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                        <th class="request-data-key small-12 large-3 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 25%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span style="color: #4D5663; font-weight: 600;">
                            {ts}Status:{/ts}
                          </span>
                        </th></tr></table></th>
                        <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span>{$leaveStatus}</span>
                        </th></tr></table></th>
                      </tr></tbody></table>

                      <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                        <th class="request-data-key small-12 large-3 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 25%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span style="color: #4D5663; font-weight: 600;">
                            {ts}Staff Member:{/ts}
                          </span>
                        </th></tr></table></th>
                        <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span>{contact.display_name}</span>
                        </th></tr></table></th>
                      </tr></tbody></table>


                      {if $leaveRequest->from_date eq $leaveRequest->to_date}
                        <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="request-data-key small-12 large-3 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 25%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            <span style="color: #4D5663; font-weight: 600;">
                              {ts}{$dateLabel}:{/ts}
                            </span>
                          </th></tr></table></th>
                          <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            {$smarty.capture.fromDate}
                          </th></tr></table></th>
                        </tr></tbody></table>

                      {else}
                        <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="request-data-key small-12 large-3 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 25%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            <span style="color: #4D5663; font-weight: 600;">
                              {ts}From {$dateLabel}:{/ts}
                            </span>
                          </th></tr></table></th>
                          <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            {$smarty.capture.fromDate}
                          </th></tr></table></th>
                        </tr></tbody></table>

                        <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                          <th class="request-data-key small-12 large-3 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 25%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            <span style="color: #4D5663; font-weight: 600;">
                              {ts}To {$dateLabel}:{/ts}
                            </span>
                          </th></tr></table></th>
                          <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                            {if $calculationUnitName === \'days\'}
                              <span>{$toDate|truncate:10:\'\'|crmDate} {$toDateType}</span>
                            {/if}
                            {if $calculationUnitName === \'hours\'}
                              <span>{$toDate|truncate:16:\'\'|crmDate}</span>
                            {/if}
                          </th></tr></table></th>
                        </tr></tbody></table>

                      {/if}
                      <hr style="Margin: 20px auto; border: 0; border-top: 1px solid #D3DEE2; height: 0; margin: 20px auto;">
                      <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                        <th class="request-data-key small-12 large-3 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 25%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span style="color: #4D5663; font-weight: 600;">
                            {ts}Additional Details:{/ts}
                          </span>
                        </th></tr></table></th>
                        <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span></span>
                        </th></tr></table></th>
                      </tr></tbody></table>

                      <table class="row" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                        <th class="request-data-key small-12 large-3 columns first" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 25%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span style="color: #4D5663; font-weight: 600;">
                            {ts}The Reason:{/ts}
                          </span>
                        </th></tr></table></th>
                        <th class="request-data-value small-12 large-6 columns last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0 !important; padding-right: 0 !important; text-align: left; width: 50%;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                          <span>{$sicknessReason}</span>
                        </th></tr></table></th>
                      </tr></tbody></table>

                      {if $leaveRequiredDocuments}
                        <div class="request-documents" style="Margin-top: 10px; margin-top: 10px;">
                          {foreach from=$sicknessRequiredDocuments item=value key=id}
                            <div class="request-document" style="Margin-bottom: 10px; margin-bottom: 10px;">
                              <span class="request-document-required" style="Margin-right: 5px; margin-right: 5px; width: 25px;">
                                {if in_array($id, $leaveRequiredDocuments)}
                                <input type="checkbox" disabled checked style="Margin: 0; margin: 0;">
                                {else}
                                <input type="checkbox" disabled style="Margin: 0; margin: 0;">
                                {/if}
                              </span>
                              <span class="request-document-title" style="color: #4D5663;">{$value}</span>
                            </div>
                          {/foreach}
                        </div>
                      {/if}
                    </th><th class="expander" style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th></tr></table>
                    <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&#xA0;</td></tr></tbody></table>
                    <table class="button expanded alert" style="Margin: 0 0 16px 0; border-collapse: collapse; border-spacing: 0; margin: 0 0 16px 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;"><tr style="padding: 0; text-align: left; vertical-align: top;"><td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100%; word-wrap: break-word;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><td style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #E6807F; border: 0px solid #E6807F; border-collapse: collapse !important; color: #FFF; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 8px 16px 8px 16px; text-align: left; vertical-align: top; width: 100%; word-wrap: break-word;"><center data-parsed="" style="min-width: 0; width: 100%;"><a href="{$leaveRequestLink}" align="center" class="float-center" style="Margin: 0; border: 0 solid #E6807F; border-radius: 3px; color: #FFF; display: inline-block; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; padding-left: 0; padding-right: 0; text-align: center; text-decoration: none; width: 100%;">View This Request</a></center></td></tr></table></td>
<td class="expander" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; vertical-align: top; visibility: hidden; width: 0; word-wrap: break-word;"></td></tr></table>
                    {if $leaveComments}
                      <hr style="Margin: 20px auto; border: 0; border-top: 1px solid #D3DEE2; height: 0; margin: 20px auto;">
                      <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <td class="callout-header" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #F3F6F7; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 15px 20px; text-align: left; vertical-align: top; word-wrap: break-word;">
                            <h2 class="callout-title" style="Margin: 0; Margin-bottom: 0; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 500; line-height: 1.53846; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">Request comments</h2>
                          </td>
                        </tr>
                      </table>

                      <table class="callout" style="Margin-bottom: 0 !important; border-collapse: collapse; border-spacing: 0; margin-bottom: 0 !important; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th class="callout-inner callout-no-padding" style="Margin: 0; background: #FFF; border: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left; width: 100%;">
                        <div class="request-comments">
                          {foreach from=$leaveComments item=value key=label}
                            <table class="request-comment" style="border-bottom: 1px solid #E8EEF0; border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                              <tbody>
                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                  <td class="request-comment-header" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: 600; hyphens: auto; line-height: 1.53846; margin: 0; padding: 20px; padding-bottom: 10px; text-align: left; vertical-align: top; word-wrap: break-word;">
                                    <span class="request-comment-author">{$value.commenter}:</span>
                                    <span class="request-comment-datetime">{$value.created_at|crmDate}</span>
                                  </td>
                                </tr>
                                <tr style="padding: 0; text-align: left; vertical-align: top;">
                                  <td class="request-comment-body" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 20px; padding-top: 0; text-align: left; vertical-align: top; word-wrap: break-word;">{$value.text}</td>
                                </tr>
                              </tbody>
                            </table>

                          {/foreach}
                        </div>
                      </th><th class="expander" style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th></tr></table>
                    {/if}
                    {if $leaveFiles}
                      <hr style="Margin: 20px auto; border: 0; border-top: 1px solid #D3DEE2; height: 0; margin: 20px auto;">
                      <table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                          <td class="callout-header" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; background: #F3F6F7; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; hyphens: auto; line-height: 1.53846; margin: 0; padding: 15px 20px; text-align: left; vertical-align: top; word-wrap: break-word;">
                            <h2 class="callout-title" style="Margin: 0; Margin-bottom: 0; color: #4D5663; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 500; line-height: 1.53846; margin: 0; margin-bottom: 0; padding: 0; text-align: left; word-wrap: normal;">Other files recorded in this request</h2>
                          </td>
                        </tr>
                      </table>

                      <table class="callout" style="Margin-bottom: 0 !important; border-collapse: collapse; border-spacing: 0; margin-bottom: 0 !important; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th class="callout-inner" style="Margin: 0; background: #FFF; border: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 20px; text-align: left; width: 100%;">
                        <div class="request-attachments">
                          {foreach from=$leaveFiles item=value key=label}
                            <div class="request-attachment" style="Margin-bottom: 10px; margin-bottom: 10px;">
                              <span class="request-attachment-name" style="color: #4D5663; font-weight: 600;">{$value.name}</span>: Added on {$value.created_at|crmDate}
                            </div>

                          {/foreach}
                        </div>
                      </th><th class="expander" style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th></tr></table>
                    {/if}
                  </th></tr></table></th>
                </tr></tbody></table>
                <table class="row collapse" style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;">
                  <th class="small-12 large-12 columns first last" style="Margin: 0 auto; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 0; padding-right: 0; text-align: left; width: 588px;"><table style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tr style="padding: 0; text-align: left; vertical-align: top;"><th style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0; text-align: left;">
                    <table class="spacer" style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;"><tbody><tr style="padding: 0; text-align: left; vertical-align: top;"><td height="16px" style="-moz-hyphens: auto; -webkit-hyphens: auto; Margin: 0; border-collapse: collapse !important; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; hyphens: auto; line-height: 16px; margin: 0; mso-line-height-rule: exactly; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">&#xA0;</td></tr></tbody></table>
                    <img class="text-center email-logo" src="https://civihr.org/sites/default/files/email-logo.png" style="-ms-interpolation-mode: bicubic; Margin: 0 auto; clear: both; display: block; float: none; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: 100px;">
                  </th>
<th class="expander" style="Margin: 0; color: #727E8A; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: normal; line-height: 1.53846; margin: 0; padding: 0 !important; text-align: left; visibility: hidden; width: 0;"></th></tr></table></th>
                </tr></tbody></table>

              </div>

            </td></tr></tbody></table>
          </center>
        </td>
      </tr>
    </table>
    <!-- prevent Gmail on iOS font size manipulation -->
   <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
  </body>
</html>',
      'msg_text' => 'CiviHR Sickness RecordSickness Request Type Name{ts}Status:{/ts}{$leaveStatus}{ts}Staff Member:{/ts}{contact.display_name}{if $leaveRequest->from_date eq $leaveRequest->to_date}{ts}Date:{/ts}{$fromDate|truncate:10:\'\'|crmDate}{$fromDateType}{else}{ts}From Date:{/ts}{$fromDate|truncate:10:\'\'|crmDate}{$fromDateType}{ts}To Date:{/ts}{$toDate|truncate:10:\'\'|crmDate}{$toDateType}{/if}Additional Details:The Reason{foreach from=$sicknessReasons item=value key=id}{if $id eq $leaveRequest->sickness_reason}{$value}{/if}{/foreach}{if $leaveRequiredDocuments}{foreach from=$sicknessRequiredDocuments item=value key=id}{if in_array($id, $leaveRequiredDocuments)}{$value}{/if}{/foreach}{/if}{if $leaveComments}Request Comments{foreach from=$leaveComments item=value key=label}{$value.commenter}:{$value.created_at|crmDate}{$value.text}{/foreach}{/if}{if $leaveFiles}Other files recorded on this request{foreach from=$leaveFiles item=value key=label}{$value.name}: Added on{$value.upload_date|crmDate}{/foreach}{/if}',
      'is_reserved' => 1
    ],
  ]
];
