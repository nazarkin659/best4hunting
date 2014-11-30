/*------------------------------------------------------------------------------------------------------------
# VP One Page Checkout! Joomla 2.5 Plugin for VirtueMart 2.0 / VirtueMart 2.6
# ------------------------------------------------------------------------------------------------------------
# Copyright (C) 2012 - 2014 VirtuePlanet Services LLP. All Rights Reserved.
# License: This JavaScript is released under VirtuePlanet Proprietary License - http://www.virtueplanet.com/policies/licenses
# Author: VirtuePlanet Services LLP
# Email: info@virtueplanet.com
# Website:  http://www.virtueplanet.com
------------------------------------------------------------------------------------------------------------*/
if (typeof ProOPC === "undefined") {
    var jq = jQuery.noConflict();
    var ProOPC = {
        spinnervars: function() {
            var e = {
                lines: 13,
                length: 3,
                width: 2,
                radius: 5,
                corners: 1,
                rotate: 0,
                direction: 1,
                color: "#FFF",
                speed: 1.5,
                trail: 60,
                shadow: false,
                hwaccel: false,
                className: "proopc-spinner",
                zIndex: 2e9,
                top: "auto",
                left: "auto"
            };
            proopc_spinner = (new Spinner(e)).spin();
            var t = {
                lines: 10,
                length: 10,
                width: 4,
                radius: 15,
                corners: 1,
                rotate: 0,
                direction: 1,
                color: window.SPINNER_COLOR,
                speed: 1.5,
                trail: 60,
                shadow: false,
                hwaccel: true,
                className: "proopc-page-loader",
                zIndex: 2e9,
                top: 20,
                left: 14
            };
            proopc_loader = (new Spinner(t)).spin();
            var n = {
                lines: 10,
                length: 5,
                width: 3,
                radius: 8,
                corners: 1,
                rotate: 0,
                direction: 1,
                color: window.SPINNER_COLOR,
                speed: 1.5,
                trail: 40,
                shadow: false,
                hwaccel: true,
                className: "proopc-area-loader",
                zIndex: 2e9,
                top: 20,
                left: 14
            };
            proopc_area_loader = (new Spinner(n)).spin()
        },
        opcmethod: function() {
            var e = jq('input:radio[name="proopc-method"]:checked').val();
            if (e == "guest") {
                jq(".proopc-reg-form").hide().css("opacity", 0);
                jq(".proopc-reg-advantages, .proopc-guest-form").show().animate({
                    opacity: 1
                }, 500);
                ProOPC.inputwidth()
            } else {
                jq(".proopc-reg-form").show().animate({
                    opacity: 1
                }, 500, function() {
                    if (jq("#ProOPC").find("#dynamic_recaptcha_1").length) {
                        ProOPC.style()
                    }
                });
                jq(".proopc-reg-advantages, .proopc-guest-form").hide().css("opacity", 0);
                ProOPC.inputwidth()
            }
        },
        guestcheckout: function() {
            jq.ajax({
                type: "POST",
                url: window.URI,
                data: jq("#GuestUser").serialize(),
                beforeSend: function() {
                    if (ProOPC.validateForm("#GuestUser") == false) {
                        return false
                    }
                    jq("#proopc-guest-process").append(proopc_spinner.el)
                },
                success: function(e) {
                    jq("#proopc-guest-process .proopc-spinner").remove();
                    var t = '<div class="proopc-alert proopc-success-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + Joomla.JText._("PLG_VPONEPAGECHECKOUT_EMAIL_SAVED") + "</div>";
                    jq("#proopc-system-message").html(t);
                    ProOPC.processCheckout({
                        error: 0
                    })
                }
            });
            return false
        },
        verifyRegForm: function() {
            jq('#UserLogin input[type="text"], #UserLogin input[type="password"]').keyup(function(e) {
                if (jq(this).val() == "") {
                    jq(this).siblings(".status").addClass("invalid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_REQUIRED_FIELD"))
                } else {
                    jq(this).siblings(".status").removeClass("invalid").removeAttr("title")
                }
            });
            jq('#GuestUser input[type="text"]').keyup(function(e) {
                var t = jq(this);
                if (jq(this).attr("id") == "email_field") {
                    var n = jq(t).val();
                    if (ProOPC.validateEmail(n)) {
                        jq(t).removeClass("invalid").addClass("valid");
                        jq(t).siblings(".status").removeClass("invalid").addClass("valid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_VALIDATED"))
                    } else {
                        jq(t).removeClass("valid").addClass("invalid");
                        jq(t).siblings(".status").removeClass("valid").addClass("invalid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_EMAIL_INVALID"))
                    }
                }
            });
            jq('#UserRegistration input[type="text"], #UserRegistration input[type="password"]').keyup(function(e) {
                var t = jq(this);
                if (jq(this).attr("id") == "email_field") {
                    var n = jq(t).val();
                    if (ProOPC.validateEmail(n)) {
                        if (window.AJAXVALIDATION == 1) {
                            jq.ajax({
                                beforeSend: function(e) {
                                    jq.emailPool.abortAll();
                                    jq.emailPool.push(e);
                                    jq(t).siblings(".status").removeClass("hover-tootip").removeClass("invalid").removeClass("valid").addClass("validating")
                                },
                                dataType: "json",
                                url: window.URI,
                                data: "ctask=checkemail&email=" + n,
                                cache: false,
                                success: function(e) {
                                    if (e.valid !== 1) {
                                        jq(t).removeClass("valid").addClass("invalid");
                                        jq(t).siblings(".status").removeClass("valid").removeClass("validating").addClass("hover-tootip").addClass("invalid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_EMAIL_ALREADY_REGISTERED"))
                                    } else {
                                        jq(t).removeClass("invalid").addClass("valid");
                                        jq(t).siblings(".status").removeClass("validating").removeClass("invalid").addClass("hover-tootip").addClass("valid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_VALIDATED"))
                                    }
                                },
                                complete: function(e) {
                                    var t = jq.emailPool.indexOf(e);
                                    if (t > -1) {
                                        jq.emailPool.splice(t, 1)
                                    }
                                }
                            })
                        } else {
                            jq(t).removeClass("invalid").addClass("valid");
                            jq(t).siblings(".status").removeClass("invalid").addClass("valid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_VALIDATED"))
                        }
                    } else {
                        jq(t).removeClass("valid").addClass("invalid");
                        jq(t).siblings(".status").removeClass("valid").addClass("invalid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_EMAIL_INVALID"))
                    }
                } else {
                    if (jq(this).attr("id") == "username_field") {
                        var r = jq(t).val();
                        if (ProOPC.validateUsername(r)) {
                            if (window.AJAXVALIDATION == 1) {
                                jq.ajax({
                                    dataType: "json",
                                    url: window.URI,
                                    data: "ctask=checkuser&username=" + r,
                                    cache: false,
                                    beforeSend: function(e) {
                                        jq.userPool.abortAll();
                                        jq.userPool.push(e);
                                        jq(t).siblings(".status").removeClass("hover-tootip").removeClass("invalid").removeClass("valid").addClass("validating")
                                    },
                                    success: function(e) {
                                        if (e.valid !== 1) {
                                            jq(t).removeClass("valid").addClass("invalid");
                                            jq(t).siblings(".status").removeClass("valid").removeClass("validating").addClass("hover-tootip").addClass("invalid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_USERNAME_ALREADY_REGISTERED"))
                                        } else {
                                            jq(t).removeClass("invalid").addClass("valid");
                                            jq(t).siblings(".status").removeClass("invalid").removeClass("validating").addClass("hover-tootip").addClass("valid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_VALIDATED"))
                                        }
                                    },
                                    complete: function(e) {
                                        var t = jq.userPool.indexOf(e);
                                        if (t > -1) {
                                            jq.userPool.splice(t, 1)
                                        }
                                    }
                                })
                            } else {
                                jq(t).removeClass("invalid").addClass("valid");
                                jq(t).siblings(".status").removeClass("invalid").addClass("valid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_VALIDATED"))
                            }
                        } else {
                            jq(t).removeClass("valid").addClass("invalid");
                            jq(t).siblings(".status").removeClass("valid").addClass("invalid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_USERNAME_INVALID"))
                        }
                    } else {
                        if (jq(this).attr("id") == "password_field") {
                            var i = jq(t).val();
                            if (i == "") {
                                jq("#password-stregth, #meter-status").removeClass().addClass("invalid");
                                jq(t).removeClass("valid").addClass("invalid");
                                jq(t).siblings(".status").removeClass("valid").addClass("invalid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_INVALID"));
                                jq("#password-stregth").text("")
                            } else {
                                ProOPC.checkStrength(i)
                            }
                        } else {
                            if (jq(this).attr("id") == "password2_field") {
                                var s = jq("#password_field").val();
                                var o = jq(t).val();
                                if (o !== s || o == "") {
                                    jq(t).removeClass("valid").addClass("invalid");
                                    jq(t).siblings(".status").removeClass("valid").addClass("invalid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_INVALID"))
                                } else {
                                    jq(t).removeClass("invalid").addClass("valid");
                                    jq(t).siblings(".status").removeClass("invalid").addClass("valid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_VALIDATED"))
                                }
                            } else {
                                if (jq(this).attr("id") == "name_field") {
                                    var u = jq(t).val();
                                    if (u.length == 0) {
                                        jq(t).removeClass("valid").addClass("invalid");
                                        jq(t).siblings(".status").removeClass("valid").addClass("invalid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_INVALID"))
                                    } else {
                                        jq(t).removeClass("invalid").addClass("valid");
                                        jq(t).siblings(".status").removeClass("invalid").addClass("valid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_VALIDATED"))
                                    }
                                }
                            }
                        }
                    }
                }
            })
        },
        validateEmail: function(e) {
            var t = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            if (!t.test(e) || e.length < 5) {
                return false
            } else {
                return true
            }
        },
        validateUsername: function(e) {
            var t = /^[a-zA-Z0-9]+$/;
            if (!t.test(e)) {
                return false
            } else {
                return true
            }
        },
        checkStrength: function(e) {
            var t = 0;
            if (e.length < 4) {
                jq("#password-stregth, #meter-status").removeClass().addClass("short");
                jq("#password_field").removeClass("valid").addClass("invalid");
                jq("#password_field").siblings(".status").removeClass("valid").addClass("invalid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_INVALID"));
                jq("#password-stregth").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_TOO_SHORT"));
                return false
            }
            if (e.length > 4) {
                t += 1
            }
            if (e.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {
                t += 1
            }
            if (e.match(/([a-zA-Z])/) && e.match(/([0-9])/)) {
                t += 1
            }
            if (e.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) {
                t += 1
            }
            if (e.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,",%,&,@,#,$,^,*,?,_,~])/)) {
                t += 1
            }
            if (t < 2) {
                jq("#password-stregth, #meter-status").removeClass().addClass("weak");
                jq("#password_field").removeClass("invalid").addClass("valid");
                jq("#password_field").siblings(".status").removeClass("invalid").addClass("valid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_VALIDATED"));
                jq("#password-stregth").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_WEAK"))
            } else {
                if (t == 2) {
                    jq("#password-stregth, #meter-status").removeClass().addClass("good");
                    jq("#password_field").removeClass("invalid").addClass("valid");
                    jq("#password_field").siblings(".status").removeClass("invalid").addClass("valid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_VALIDATED"));
                    jq("#password-stregth").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_GOOD"))
                } else {
                    jq("#password-stregth, #meter-status").removeClass().addClass("strong");
                    jq("#password_field").removeClass("invalid").addClass("valid");
                    jq("#password_field").siblings(".status").removeClass("invalid").addClass("valid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_VALIDATED"));
                    jq("#password-stregth").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_STRONG"))
                }
            }
        },
        registerCheckout: function() {
            var e = "&option=com_virtuemart&address_type=BT&task=saveUser&controller=user&ctask=register";
            jq.ajax({
                dataType: "json",
                type: "POST",
                beforeSend: function() {
                    if (ProOPC.validateForm("#UserRegistration") == false) {
                        return false
                    }
                    jq("#proopc-register-process").append(proopc_spinner.el)
                },
                url: window.URI,
                data: jq("#UserRegistration").serialize() + e,
                success: function(e) {
                    if (e.error == 1) {
                        jq("#proopc-register-process .proopc-spinner").remove();
                        var t = '<div class="proopc-alert proopc-alert-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + e.msg.join("<br/>") + "</div>";
                        jq("#proopc-system-message").html(t);
                        jq("html,body").animate({
                            scrollTop: jq("#proopc-system-message").offset().top - 50
                        }, 500);
                        if (typeof Recaptcha !== "undefined") {
                            Recaptcha.reload()
                        }
                    } else {
                        var t = '<div class="proopc-alert proopc-success-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + e.msg.join("<br/>") + "</div>";
                        jq("#proopc-system-message").html(t);
                        jq("html,body").animate({
                            scrollTop: jq("#proopc-system-message").offset().top - 50
                        }, 500);
                        if (e.stop == 1) {
                            jq("#proopc-register-process .proopc-spinner").remove()
                        } else {
                            setTimeout(function() {
                                jq("#proopc-register-process .proopc-spinner").remove();
                                if (window.RELOAD) {
                                    window.location.reload()
                                } else {
                                    ProOPC.processCheckout(e)
                                }
                            }, 3e3)
                        }
                    }
                },
                error: function() {
                    jq("#proopc-register-process .proopc-spinner").remove();
                    var e = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>Error submiting registration form (registerCheckout). Reload the page and try again.</div>';
                    jq("#proopc-system-message").html(e);
                    jq("html,body").animate({
                        scrollTop: jq("#proopc-system-message").offset().top - 50
                    }, 500)
                }
            });
            return false
        },
        processCheckout: function(e) {
            ProOPC.addpageloader();
            jq("#proopc-page-spinner").after('<div id="proopc-order-process"></div>');
            jq("#proopc-order-process").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_PLEASE_WAIT"));
            if (e.error == 0) {
                var e = jq("#ProOPC").html();
                var t = ProOPC.getUrlVars();
                jq.ajax({
                    url: window.URI,
                    data: "task=procheckout&" + t,
                    dataType: "html",
                    success: function(e) {
                        var t = jq(e).find("#ProOPC").html();
                        if (t == null) {
                            var t = e
                        }
                        jq("#ProOPC").html(t);
                        if (!window.RELOAD) {
                            jq("html, body").animate({
                                scrollTop: jq("#proopc-system-message").offset().top - 100
                            }, 500)
                        }
                    },
                    complete: function() {
                        var e = jq("input#BTStateID").val();
                        var t = jq("input#STStateID").val();
                        jq("#virtuemart_country_id").vm2front("list", {
                            dest: "#virtuemart_state_id",
                            ids: e,
                            prefiks: ""
                        });
                        jq("#shipto_virtuemart_country_id").vm2front("list", {
                            dest: "#shipto_virtuemart_state_id",
                            ids: t,
                            prefiks: "shipto_"
                        });
                        jq("#virtuemart_country_id").change(function() {
                            if (jq("#virtuemart_state_id optgroup").length > 0) {
                                jq("#virtuemart_state_id optgroup").remove();
                                jq("#virtuemart_country_id").vm2front("list", {
                                    dest: "#virtuemart_state_id",
                                    ids: "",
                                    prefiks: ""
                                })
                            }
                        });
                        jq("#shipto_virtuemart_country_id").change(function() {
                            if (jq("#shipto_virtuemart_state_id optgroup").length > 0) {
                                jq("#shipto_virtuemart_state_id optgroup").remove();
                                jq("#shipto_virtuemart_country_id").vm2front("list", {
                                    dest: "#shipto_virtuemart_state_id",
                                    ids: "",
                                    prefiks: "shipto_"
                                })
                            }
                        });
                        ProOPC.style();
                        ProOPC.tooltip();
                        ProOPC.inputwidth();
                        ProOPC.selectwidth();
                        jq("#proopc-order-process").remove();
                        ProOPC.removepageloader();
                        ProOPC.defaultSP();
                        if (typeof klarna === "undefined") {
                            klarnaExits = false
                        } else {
                            klarnaExits = true
                        }
                        if (klarnaExits) {
                            ProOPC.loadPaymentScripts()
                        }
                        ProOPC.loadShipmentScripts()
                    }
                })
            } else {
                var n = "";
                jq.each(e.msg, function(e, t) {
                    n = n + '<div class="error-msg">' + t + "</div>"
                });
                jq("#proopc-system-message").html(n);
                jq("html,body").animate({
                    scrollTop: jq("#proopc-system-message").offset().top - 100
                }, 500);
                jq("#proopc-system-message").children("span").animate({
                    opacity: 0
                }, 2e3)
            }
        },
        loginAjax: function() {
            if (ProOPC.validateForm("#UserLogin") == false) {
                return false
            }
            var e = jq(".proops-login-inputs input:last").attr("name");
            var t = encodeURIComponent(jq(".proops-login-inputs input:last").val());
            var n = "ctask=login&username=" + encodeURIComponent(jq("#proopc-username").val()) + "&passwd=" + encodeURIComponent(jq("#proopc-passwd").val()) + "&" + e + "=" + t + "&return=" + encodeURIComponent(jq("#proopc-return").val());
            if (jq("#proopc-remember").is(":checked")) {
                n += "&remember=yes"
            }
            jq.ajax({
                type: "POST",
                beforeSend: function() {
                    jq("#proopc-login-process").append(proopc_spinner.el)
                },
                url: window.URI,
                data: n,
                success: function(e, t, n) {
                    if (e == "1" || e == 1) {
                        jq("#proopc-login-process .proopc-spinner").remove();
                        var r = '<div class="proopc-alert proopc-success-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + Joomla.JText._("PLG_VPONEPAGECHECKOUT_LOGIN_COMPLETED") + "</div>";
                        jq("#proopc-system-message").html(r);
                        if (window.RELOAD) {
                            window.location.reload()
                        } else {
                            ProOPC.processCheckout({
                                error: 0
                            })
                        }
                    } else {
                        if (e.indexOf("</head>") == -1) {
                            jq("#proopc-login-process .proopc-spinner").remove();
                            var r = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + Joomla.JText._("JLIB_LOGIN_AUTHENTICATE") + "</div>";
                            jq("#proopc-system-message").html(r)
                        } else {
                            jq("#proopc-login-process .proopc-spinner").remove();
                            var r = '<div class="proopc-alert proopc-success-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + Joomla.JText._("PLG_VPONEPAGECHECKOUT_LOGIN_COMPLETED") + "</div>";
                            jq("#proopc-system-message").html(r);
                            if (window.RELOAD) {
                                window.location.reload()
                            } else {
                                ProOPC.processCheckout({
                                    error: 0
                                })
                            }
                        }
                    }
                },
                error: function(e, t, n) {
                    jq("#proopc-login-process .proopc-spinner").remove();
                    var r = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>Login failed. Please refersh the page and try again.</div>';
                    jq("#proopc-system-message").html(r)
                }
            });
            return false
        },
        updateBTaddress: function(e) {
            jq("#proopc-order-submit").attr("disabled", "disabled");
            if (jq(e).attr("id") == "virtuemart_country_id" || jq(e).attr("id") == "virtuemart_state_id") {
                var t = jq(e).val();
                jq(e).find('[selected="selected"]').removeAttr("selected");
                jq(e).find('option[value="' + t + '"]').attr("selected", "selected")
            }
            var n = jq("#EditBTAddres").serialize();
            var r = jq("#formToken input").attr("name");
            var i = jq("#formToken input").val();
            var s = "&option=com_virtuemart&task=saveUser&controller=user&ctask=savebtaddress&" + r + "=" + i;
            jq.ajax({
                type: "POST",
                url: window.URI,
                data: n + s,
                success: function(e) {
                    ProOPC.getshipmentpaymentcartlist()
                }
            })
        },
        updateSTaddress: function(e) {
            jq("#proopc-order-submit").attr("disabled", "disabled");
            if (jq(e).attr("id") == "shipto_virtuemart_country_id" || jq(e).attr("id") == "shipto_virtuemart_state_id") {
                var t = jq(e).val();
                jq(e).find('[selected="selected"]').removeAttr("selected");
                jq(e).find('option[value="' + t + '"]').attr("selected", "selected")
            }
            var n = jq("#EditSTAddres").serialize();
            var r = jq("#formToken input").attr("name");
            var i = jq("#formToken input").val();
            var s = "&option=com_virtuemart&task=saveUser&controller=user&ctask=savestaddress&" + r + "=" + i;
            jq.ajax({
                type: "POST",
                url: window.URI,
                data: n + s,
                success: function(e) {
                    ProOPC.getshipmentpaymentcartlist()
                }
            })
        },
        selectSTAddress: function(e) {
            var t = jq(e).val();
            jq.ajax({
                beforeSend: function(e) {
                    ProOPC.addloader("#proopc-st-address")
                },
                dataType: "json",
                type: "POST",
                url: window.URI,
                data: "ctask=selectstaddress&virtuemart_userinfo_id=" + t,
                cache: false,
                success: function(e) {
                    jq("#proopc-st-address").html(e.editst);
                    jq("#shipto_virtuemart_country_id").vm2front("list", {
                        dest: "#shipto_virtuemart_state_id",
                        ids: '"' + e.stateid + '"',
                        prefiks: "shipto_"
                    })
                },
                complete: function(e) {
                    ProOPC.style();
                    ProOPC.inputwidth();
                    ProOPC.selectwidth();
                    ProOPC.removeloader("#proopc-st-address");
                    ProOPC.getshipmentpaymentcartlist()
                }
            })
        },
        setst: function(e) {
            jq("#proopc-order-submit").attr("disabled", "disabled");
            if (jq(e).length > 0) {
                if (e.checked) {
                    jq.ajax({
                        beforeSend: function(e) {
                            jq(".proopc-st-address .edit-address").slideUp();
                            jq.xhrPool.abortAll();
                            jq.xhrPool.push(e)
                        },
                        type: "post",
                        url: window.URI,
                        data: "ctask=btasst",
                        cache: false,
                        success: function() {
                            ProOPC.getshipmentpaymentcartlist()
                        },
                        complete: function(e) {
                            var t = jq.xhrPool.indexOf(e);
                            if (t > -1) {
                                jq.xhrPool.splice(t, 1)
                            }
                        }
                    })
                } else {
                    jq.ajax({
                        beforeSend: function(e) {
                            jq(".proopc-st-address .edit-address").slideDown();
                            jq.xhrPool.abortAll();
                            jq.xhrPool.push(e);
                            ProOPC.inputwidth();
                            ProOPC.selectwidth()
                        },
                        type: "post",
                        url: window.URI,
                        data: "ctask=btnotasst",
                        cache: false,
                        success: function(e) {
                            ProOPC.updateSTaddress()
                        },
                        complete: function(e) {
                            var t = jq.xhrPool.indexOf(e);
                            if (t > -1) {
                                jq.xhrPool.splice(t, 1)
                            }
                        }
                    })
                }
            }
        },
        getshipmentpaymentcartlist: function() {
            ProOPC.addloader("#proopc-pricelist, #proopc-payments, #proopc-shipments");
            jq.ajax({
                dataType: "json",
                url: window.URI,
                data: "ctask=getshipmentpaymentcartlist",
                cache: false,
                success: function(e) {
                    jq("#proopc-shipments").html(e.shipments);
                    jq("#proopc-payments").html(e.payments);
                    jq("#proopc-pricelist").html(e.cartlist);
                    if (e.payment_scripts.length > 0 && e.payment_scripts[0] !== "") {
                        payment_scripts = e.payment_scripts
                    }
                    if (e.payment_script[0] !== "") {
                        jq.each(e.payment_script, function(e, t) {
                            jq.getScript(t, function(e, t, n) {
                                if (typeof payment_scripts !== "undefined") {
                                    jq.each(payment_scripts, function(e, t) {
                                        t
                                    })
                                }
                            })
                        })
                    }
                    if (e.shipment_scripts.length > 0 && e.shipment_scripts[0] !== "") {
                        jq.each(e.shipment_scripts, function(e, t) {
                            jq("head").append('<script type="text/javascript">' + t + "</script>")
                        })
                    }
                },
                complete: function() {
                    ProOPC.style();
                    ProOPC.tooltip();
                    ProOPC.removeloader("#proopc-pricelist, #proopc-payments, #proopc-shipments");
                    ProOPC.defaultSP()
                },
                error: function() {
                    console.log("Error: Error gettings Shipments, Payments and Cartlist (getshipmentpaymentcartlist).")
                }
            })
        },
        defaultSP: function() {
            var e = "";
            if (window.AUTOSHIPMENT) {
                if (jq("#proopc-shipments input:radio[name=virtuemart_shipmentmethod_id]").length) {
                    var t = false;
                    if (jq("#proopc-savedShipment").val()) {
                        jq("#proopc-shipments input:radio[name=virtuemart_shipmentmethod_id]").each(function() {
                            if (jq(this).val() == jq("#proopc-savedShipment").val()) {
                                jq(this).attr("checked", true);
                                t = true;
                                return false
                            }
                        })
                    }
                    if (!t) {
                        jq("#proopc-shipments input:radio[name=virtuemart_shipmentmethod_id]:first").attr("checked", true);
                        var n = jq("#proopc-shipments input:radio[name=virtuemart_shipmentmethod_id]:checked").val();
                        e = e + "&virtuemart_shipmentmethod_id=" + n;
                        var r = jq("#proopc-shipments input:radio[name=virtuemart_shipmentmethod_id]:checked").data("usps");
                        if (jq("#usps_name-" + n).length) {
                            var i = "usps_name-" + n + "=" + r.service;
                            e = e + "&" + i
                        }
                        if (jq("#usps_rate-" + n).length) {
                            var s = "usps_rate-" + n + "=" + r.rate;
                            e = e + "&" + s
                        }
                    }
                }
            }
            var o = jq("#proopc-payments input:radio[name=virtuemart_paymentmethod_id]:first").attr("data-paypalproduct");
            if (window.AUTOPAYMENT && o != "exp") {
                if (jq("#proopc-payments input:radio[name=virtuemart_paymentmethod_id]").length) {
                    var u = false;
                    if (jq("#proopc-savedPayment").val()) {
                        jq("#proopc-payments input:radio[name=virtuemart_paymentmethod_id]").each(function() {
                            if (jq(this).val() == jq("#proopc-savedPayment").val()) {
                                jq(this).attr("checked", true);
                                u = true;
                                return false
                            }
                        })
                    }
                    if (!u) {
                        jq("#proopc-payments input:radio[name=virtuemart_paymentmethod_id]:first").attr("checked", true);
                        var a = jq("#proopc-payments input:radio[name=virtuemart_paymentmethod_id]:checked").data("pmtype");
                        jq(".vmpayment_cardinfo").removeClass("show").addClass("hide");
                        jq(".vmpayment_cardinfo." + a).removeClass("hide").addClass("show");
                        var f = jq("#proopc-payments input:radio[name=virtuemart_paymentmethod_id]:checked").val();
                        var l = 0;
                        if (jq(".vmpayment_cardinfo." + a).length > 0 || jq("#proopc-payments input:radio[name=virtuemart_paymentmethod_id]:checked").data("pmtype") == "sisowideal") {
                            l = 1
                        } else {
                            if (jq("#proopc-payments input:radio[name=virtuemart_paymentmethod_id]:checked").hasClass("klarnaPayment")) {
                                var c = jq("#proopc-payments input:radio[name=virtuemart_paymentmethod_id]:checked").next('input[name="klarna_paymentmethod"]').val();
                                l = 1 + "&klarna_paymentmethod=" + c
                            }
                        }
                        var h = "";
                        if (jq("#proopc-payments input:radio[name=virtuemart_paymentmethod_id]:checked").data("pmtype") == "piraeus") {
                            jq(".vmpayment_cardinfo.piraeus").find("select, input").each(function() {
                                h = h + "&" + jq(this).attr("name") + "=" + jq(this).val()
                            })
                        }
                        e = e + "&virtuemart_paymentmethod_id=" + f + "&payment_data=" + l + h
                    }
                }
            }
            if (e !== "") {
                jq.ajax({
                    dataType: "json",
                    url: window.URI,
                    data: "ctask=setdefaultsp&ajax=1" + e,
                    cache: false,
                    beforesend: function() {
                        jq("#proopc-order-submit").attr("disabled", "disabled")
                    },
                    success: function(e) {
                        if (e.error) {
                            jq("#proopc-order-submit").removeAttr("disabled");
                            console.log("Error: Setting default Shipment & Payment Method. Please select manually.")
                        } else {
                            ProOPC.getcartlist()
                        }
                    }
                })
            }
        },
        setshipment: function(e) {
            jq("#proopc-order-submit").attr("disabled", "disabled");
            if (jq(e).length > 0) {
                var t = jq(e).val();
                var n = "&virtuemart_shipmentmethod_id=" + t;
                var r = jq(e).data("usps");
                if (jq("#usps_name-" + t).length) {
                    var i = "usps_name-" + t + "=" + r.service;
                    n = n + "&" + i
                }
                if (jq("#usps_rate-" + t).length) {
                    var s = "usps_rate-" + t + "=" + r.rate;
                    n = n + "&" + s
                }
                var o = jq(e).data("ups");
                if (jq("#ups_rate-" + t).length) {
                    var u = "ups_rate-" + t + "=" + o.id;
                    n = n + "&" + u
                }
                jq.ajax({
                    dataType: "json",
                    url: window.URI,
                    data: "ctask=setshipments" + n,
                    cache: false,
                    success: function(e) {
                       /* if (e.error) {
                             var t = '<div class="proopc-alert proopc-success-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + e.msg + "</div>";
                            jq("#proopc-system-message").html(t);
                            jq("html,body").animate({
                                scrollTop: jq("#proopc-system-message").offset().top - 100
                            }, 500)

                        } else {
                            ProOPC.getcartlist();
                            if (window.RELOADPAYMENTS == 1) {
                                ProOPC.getpayments()
                            }
                        }
                        */
                        ProOPC.getcartlist();
                            if (window.RELOADPAYMENTS == 1) {
                                ProOPC.getpayments()
                            }
                    },
                    error: function() {
                        console.log("Shipment Method selection problem (setshipment)");
                        jq("#proopc-order-submit").removeAttr("disabled")
                    }
                })
            }
        },
        setpayment: function(e) {
            jq("#proopc-order-submit").attr("disabled", "disabled");
            if (jq(e).is(":checked")) {
                var t = jq(e).data("pmtype");
                jq(".klarna_box_bottom").hide();
                jq(".vmpayment_cardinfo").removeClass("show").addClass("hide");
                jq(".vmpayment_cardinfo." + t).removeClass("hide").addClass("show")
            }
            if (jq(e).is(":checked") && jq(e).hasClass("klarnaPayment")) {
                ProOPC.klarnaOpenClose(e)
            }
            if (jq(e).length > 0) {
                var n = jq(e).val();
                var r = 0;
                if (jq(".vmpayment_cardinfo." + t).length > 0 || jq(e).data("pmtype") == "sisowideal") {
                    r = 1
                } else {
                    if (jq(e).hasClass("klarnaPayment")) {
                        var i = jq(e).next('input[name="klarna_paymentmethod"]').val();
                        r = 1 + "&klarna_paymentmethod=" + i
                    }
                }
                var s = "";
                if (jq(e).data("pmtype") == "piraeus") {
                    jq(".vmpayment_cardinfo.piraeus").find("select, input").each(function() {
                        s = s + "&" + jq(this).attr("name") + "=" + jq(this).val()
                    })
                }
                if (jq(e).data("paypalproduct") == "exp") {
                    var t = '<div class="proopc-alert proopc-info-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + Joomla.JText._("VMPAYMENT_PAYPAL_REDIRECT_MESSAGE") + "</div>";
                    jq("#proopc-system-message").html(t);
                    jq("html,body").animate({
                        scrollTop: jq("#proopc-system-message").offset().top - 100
                    }, 500)
                }
                jq.ajax({
                    dataType: "json",
                    url: window.URI,
                    data: "ctask=setpayment&ajax=1&virtuemart_paymentmethod_id=" + n + "&payment_data=" + r + s,
                    cache: false,
                    type: "post",
                    success: function(e) {
                        if (e.error) {
                            var t = '<div class="proopc-alert proopc-warning-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + e.msg + "</div>";
                            jq("#proopc-system-message").html(t);
                            jq("html,body").animate({
                                scrollTop: jq("#proopc-system-message").offset().top - 100
                            }, 500);
                            console.log(e)
                        } else if (e.redirect != false) {
                            ProOPC.getcartlist(e.redirect)
                        } else {
                            ProOPC.getcartlist()
                        }
                    },
                    error: function(e, t, n) {
                        console.log("Payment Method selection problem (setpayment)");
                        jq("#proopc-order-submit").removeAttr("disabled")
                    }
                })
            }
        },
        getcartlist: function(e) {
            ProOPC.addloader("#proopc-pricelist");
            jq.ajax({
                dataType: "json",
                url: window.URI,
                data: "ctask=getcartlist",
                cache: false,
                success: function(e) {
                    jq("#proopc-pricelist").html(e.cartlist)
                },
                complete: function() {
                    ProOPC.style();
                    if (typeof e !== "undefined") {
                        window.location = e
                    }
                    ProOPC.removeloader("#proopc-pricelist")
                },
                error: function() {
                    console.log("Carlist Error: Error getting Cartlist Data (getcartlist).")
                }
            })
        },
        getpayments: function() {
            ProOPC.addloader("#proopc-payments");
            jq.ajax({
                dataType: "json",
                url: window.URI,
                data: "ctask=getpaymentlist",
                cache: false,
                success: function(e) {
                    jq("#proopc-payments").html(e.payments);
                    if (e.payment_scripts.length > 0 && e.payment_scripts[0] !== "") {
                        payment_scripts = e.payment_scripts
                    }
                    if (e.payment_script[0] !== "") {
                        jq.each(e.payment_script, function(e, t) {
                            jq.getScript(t, function(e, t, n) {
                                if (typeof payment_scripts !== "undefined") {
                                    jq.each(payment_scripts, function(e, t) {
                                        t
                                    })
                                }
                            })
                        })
                    }
                },
                complete: function() {
                    ProOPC.style();
                    ProOPC.tooltip();
                    ProOPC.removeloader("#proopc-payments");
                    ProOPC.defaultSP()
                },
                error: function() {
                    console.log("Error: Error gettings Payments (getpayments).")
                }
            })
        },
        deleteproduct: function(e) {
            var t = jq(e).attr("data-vpid");
            jq.ajax({
                beforeSend: function() {
                    ProOPC.addloader("#proopc-pricelist, #proopc-payments, #proopc-shipments")
                },
                type: "POST",
                url: window.URI,
                data: "ctask=deleteproduct&id=" + t,
                cache: false,
                success: function(e) {
                    jq("#proopc-system-message").html("");
                    jq(".proopc-product-hover").addClass("hide");
                    if (e.pqty == 0) {
                        window.location.reload();
                        return false
                    }
                    if (jq("input#proopc-cart-summery").length > 0) {
                        ProOPC.getcartsummery();
                        jq("#proopc-cart-totalqty").text(e.pqty)
                    } else {
                        ProOPC.getshipmentpaymentcartlist();
                        if (jq("#proopc-cart-totalqty").length > 0) {
                            jq("#proopc-cart-totalqty").text(e.pqty)
                        }
                    }
                    var t = jq(".vmCartModule");
                    jq.getJSON(vmSiteurl + "index.php?option=com_virtuemart&nosef=1&view=cart&task=viewJS&format=json" + vmLang, function(e, n) {
                        if (e.totalProduct > 0) {
                            t.find(".vm_cart_products").html("");
                            jq.each(e.products, function(e, n) {
                                jq("#hiddencontainer .container").clone().appendTo(".vmCartModule .vm_cart_products");
                                jq.each(n, function(e, n) {
                                    if (jq("#hiddencontainer .container ." + e)) {
                                        t.find(".vm_cart_products ." + e + ":last").html(n)
                                    }
                                })
                            });
                            t.find(".total").html(e.billTotal);
                            t.find(".show_cart").html(e.cart_show)
                        } else {
                            t.find(".vm_cart_products").html("");
                            t.find(".total").html(e.billTotal)
                        }
                        t.find(".total_products").html(e.totalProductTxt)
                    })
                }
            });
            return false
        },
        updateproductqty: function(e) {
            var t = jq(e).parent(".proopc-input-append").find('input[name="quantity"]').attr("data-vpid");
            var n = jq(e).parent(".proopc-input-append").find('input[name="quantity"]').val();
            jq.ajax({
                beforeSend: function() {
                    ProOPC.addloader("#proopc-pricelist, #proopc-payments, #proopc-shipments")
                },
                dataType: "JSON",
                url: window.URI,
                data: "ctask=updateproduct&id=" + t + "&quantity=" + n,
                cache: false,
                success: function(e) {
                    if (e.error !== 0) {
                        if (e.pqty == 0) {
                            window.location.reload();
                            return false
                        }
                        var t = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + e.msg + "</div>";
                        jq("#proopc-system-message").html(t);
                        jq("html,body").animate({
                            scrollTop: jq("#proopc-system-message").offset().top - 100
                        }, 500);
                        if (jq("input#proopc-cart-summery").length > 0) {
                            ProOPC.getcartsummery()
                        } else {
                            ProOPC.getshipmentpaymentcartlist()
                        }
                    } else {
                        jq("#proopc-system-message").html("");
                        jq(".proopc-product-hover").addClass("hide");
                        if (jq("input#proopc-cart-summery").length > 0) {
                            ProOPC.getcartsummery();
                            jq("#proopc-cart-totalqty").text(e.pqty)
                        } else {
                            ProOPC.getshipmentpaymentcartlist();
                            jq("#proopc-cart-totalqty").text(e.pqty)
                        }
                        if (jq(".vmCartModule")[0]) {
                            Virtuemart.productUpdate(jq(".vmCartModule"))
                        }
                    }
                }
            });
            return false
        },
        getcartsummery: function() {
            ProOPC.addloader("#proopc-pricelist");
            jq.ajax({
                dataType: "json",
                url: window.URI,
                data: "ctask=getcartsummery",
                cache: false,
                success: function(e) {
                    jq("#proopc-cart-qty").text(e.pqty);
                    jq("#proopc-pricelist").html(e.cartsummery)
                },
                complete: function() {
                    ProOPC.style();
                    ProOPC.removeloader("#proopc-pricelist")
                },
                error: function() {
                    console.log("Carlist Error: Error getting Cartlist Data (getcartlist).")
                }
            })
        },
        inputwidth: function() {
            if (window.GROUPING) {
                if (jq(".title-group").length && jq(".first_name-group").length) jq(".title-group, .first_name-group").wrapAll('<div class="proopc-row group-enabled" />');
                if (jq(".middle_name-group").length && jq(".last_name-group").length) jq(".middle_name-group, .last_name-group").wrapAll('<div class="proopc-row group-enabled" />');
                if (jq(".zip-group").length && jq(".city-group").length) jq(".zip-group, .city-group").wrapAll('<div class="proopc-row group-enabled" />');
                if (jq(".shipto_middle_name-group").length && jq(".shipto_last_name-group").length) jq(".shipto_middle_name-group, .shipto_last_name-group").wrapAll('<div class="proopc-row group-enabled" />');
                if (jq(".shipto_zip-group").length && jq(".shipto_city-group").length) jq(".shipto_zip-group, .shipto_city-group").wrapAll('<div class="proopc-row group-enabled" />')
            }
            jq('.proopc-bt-address input[type="text"], .proopc-st-address input[type="text"]').each(function() {
                var e = jq(this).parent(".inner").width();
                jq(this).width(e - 15)
            });
            jq('.proopc-register-login input[type="text"], .proopc-register-login input[type="password"]').each(function() {
                var e = jq(this).parent(".proopc-input").width();
                jq(this).width(e - 27)
            });
            jq(".proopc-register-login button").each(function() {
                var e = jq(this).parent(".proopc-input").outerWidth(true);
                jq(this).width(e)
            });
            var e = jq("#proopc-coupon .proopc-input-append").width();
            var t = jq("#proopc-coupon").find("button.proopc-btn").outerWidth(true);
            jq("#proopc-coupon-code").width(e - t - 20).css("margin-right", 5)
        },
        selectwidth: function() {
            jq(".proopc-bt-address select, .proopc-st-address select").each(function() {
                var e = jq(this).parent(".inner").width();
                jq(this).width(e - 3)
            })
        },
        productdetails: function() {
            var e = {
                interval: 100,
                sensitivity: 4,
                over: ProOPC.openproductdetails,
                timeout: 200,
                out: ProOPC.closeproductdetails
            };
            jq(".proopc-cart-product").hoverIntent(e)
        },
        openproductdetails: function() {
            var e = jq(this).attr("data-details");
            jq(this).addClass("open");
            var t = jq(this).width();
            jq(this).find(".proopc-p-info-table").width(t);
            var n = jq(this).position().top;
            var r = jq(this).height();
            jq("#" + e).show().animate({
                opacity: 1,
                top: n + r
            }, 300, "easeOutExpo")
        },
        closeproductdetails: function() {
            var e = jq(this).attr("data-details");
            jq(this).removeClass("open");
            jq("#" + e).animate({
                opacity: 0,
                top: 0
            }, 300, "easeInExpo", function() {
                jq(this).hide()
            })
        },
        savecoupon: function(e) {
            var t = jq("#proopc-coupon-code").val();
            var n = jq("#proopc-coupon-code").attr("data-default");
            if (t == n) {
                ProOPC.setmsg(1, Joomla.JText._("PLG_VPONEPAGECHECKOUT_COUPON_EMPTY"))
            } else {
                var r = "ctask=setcoupon&coupon_code=" + encodeURIComponent(t);
                jq.ajax({
                    beforeSend: function() {
                        jq("#proopc-order-submit").attr("disabled", "disabled");
                        ProOPC.addloader("#proopc-coupon");
                        jq("#proopc-coupon-process").append(proopc_spinner.el)
                    },
                    dataType: "json",
                    url: window.URI,
                    data: r,
                    success: function(e) {
                        ProOPC.setmsg(e.error, e.msg);
                        if (window.RELOADALLFORCOUPON == 1) {
                            ProOPC.getshipmentpaymentcartlist()
                        } else {
                            ProOPC.getcartlist()
                        }
                    },
                    error: function() {
                        ProOPC.removeloader("#proopc-coupon");
                        ProOPC.setmsg(1, "Coupon Error: Data could not be fetched.")
                    },
                    complete: function() {
                        ProOPC.removeloader("#proopc-coupon")
                    }
                })
            }
            return false
        },
        setmsg: function(e, t) {
            if (e == "1") {
                var n = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + t + "</div>"
            } else {
                var n = '<div class="proopc-alert proopc-success-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + t + "</div>"
            }
            jq("#proopc-system-message").html(n);
            jq("html,body").animate({
                scrollTop: jq("#proopc-system-message").offset().top - 100
            }, 500)
        },
        addloader: function(e) {
            var t = '<div class="proopc-loader-overlay"></div><div class="proopc-area-loader"><span></span></div>';
            jq(e).each(function() {
                if (jq(this).find(".proopc-area-loader").length == 0) {
                    jq(this).append(t);
                    jq(".proopc-area-loader > span").append(proopc_area_loader.el)
                }
            });
            jq("#header .navigation.sticky").css("z-index", 2e9);
            jq("#proopc-order-submit").attr("disabled", "disabled")
        },
        removeloader: function(e) {
            jq(e).each(function() {
                if (jq(this).find(".proopc-loader-overlay").length > 0) {
                    jq(this).find(".proopc-loader-overlay").remove();
                    jq(this).find(".proopc-area-loader").remove()
                }
            });
            jq("#header .navigation.sticky").css("z-index", "");
            jq("#proopc-order-submit").removeAttr("disabled")
        },
        addpageloader: function() {
            if (jq("#proopc-page-overlay").length == 0) {
                jq("body").append('<div id="proopc-page-overlay"></div><div id="proopc-page-spinner"><span></span></div>')
            }
            jq("#header .navigation.sticky").css("z-index", 2e9);
            var e = jq("body").outerHeight();
            jq("#proopc-page-overlay").css({
                display: "block",
                height: e
            }).animate({
                opacity: .7
            }, 300);
            jq("#proopc-page-spinner > span").append(proopc_loader.el)
        },
        removepageloader: function() {
            if (jq("#proopc-page-overlay").length > 0) {
                jq("#proopc-page-overlay, #proopc-page-spinner").remove()
            }
            jq("#header .navigation.sticky").css("z-index", "")
        },
        tooltip: function() {
            jq(".hover-tootip").hover(function() {
                var e = jq(this).attr("title");
                jq(this).data("tipText", e).removeAttr("title");
                if (e.indexOf("::") >= 0) {
                    var e = e.split("::");
                    var t = '<div class="tooltip-title">' + e[0] + '</div><div class="tooltip-body">' + e[1] + "</div>";
                    jq('<p class="proopc-tooltip"></p>').html(t).appendTo("body").fadeIn("slow")
                } else {
                    var t = '<div class="tooltip-body">' + e + "</div>";
                    jq('<p class="proopc-tooltip"></p>').html(t).appendTo("body").fadeIn("slow")
                }
            }, function() {
                jq(this).attr("title", jq(this).data("tipText"));
                jq(".proopc-tooltip").remove()
            }).mousemove(function(e) {
                var t = e.pageX + 20;
                var n = e.pageY + 10;
                jq(".proopc-tooltip").css({
                    top: n,
                    left: t
                })
            })
        },
        style: function() {
            var e = 0;
            jq(".proopc-register > .proopc-inner, .proopc-login > .proopc-inner").css("min-height", "");
            jq(".proopc-register > .proopc-inner, .proopc-login > .proopc-inner").each(function() {
                if (jq(this).height() > e) {
                    e = jq(this).height()
                }
            });
            jq(".proopc-register > .proopc-inner, .proopc-login > .proopc-inner").css("min-height", e);
            if (jq("#ProOPC").find("#dynamic_recaptcha_1").length) {
                var t = jq("#ProOPC #dynamic_recaptcha_1").find("table");
                var n = t.width();
                jq(t).parents(".proopc-input").width(n);
                var r = jq(".proopc-register > .proopc-inner").width() - 35;
                jq(t).parents(".proopc-input").siblings(".proopc-input-group-level").width(r - n)
            }
            jq(".proopc-p-price > div, .proopc-taxcomponent > div, .proopc-p-discount > div").each(function() {
                if (jq(this).is(":visible")) {
                    jq(this).css("display", "inline")
                }
            });
            jq(".proopc-login-message-cont").hover(function() {
                jq(".proopc-logout-cont").removeClass("hide")
            }, function() {
                jq(".proopc-logout-cont").addClass("hide")
            });
            jq(".proopc-logout-cont").width(jq(".proopc-loggedin-user").width());
            if (typeof window.bonusCartItemIds === "undefined") {} else {
                jq.each(window.bonusCartItemIds, function(e, t) {
                    if (!t.userCanUpdateQuantity) {
                        updateform = jq('input[data-vpid="' + t.cartItemId + '"]').parent(".proopc-input-append");
                        var n = jq(updateform).children('input[name="quantity"]').val();
                        if (!jq(updateform).hasClass("bonusSet")) {
                            updateform.before(n);
                            jq(updateform).addClass("bonusSet")
                        }
                        updateform.hide();
                        updateform.parent().find("button.remove_from_cart").hide()
                    }
                })
            }
            if (window.STYLERADIOCHEBOX == 1) {
                jq('#UserRegistration input[type="radio"], #EditBTAddres input[type="radio"], #EditSTAddres input[type="radio"]').each(function() {
                    jq(this).css("float", "left");
                    jq('label[for="' + jq(this).attr("id") + '"').addClass("proopc-radio-label")
                });
                jq('#UserRegistration input[type="checkbox"], #EditBTAddres input[type="checkbox"], #EditSTAddres input[type="checkbox"]').each(function() {
                    jq(this).css({
                        "float": "left",
                        margin: "4px 5px 0 0"
                    });
                    jq(this).parent(".proopc-input-append").css("padding-top", "4px");
                    jq(this).siblings("br").remove();
                    jq('label[for="' + jq(this).attr("id") + '"').css({
                        "float": "left",
                        "padding-left": "10px",
                        "padding-right": "10px"
                    }).insertAfter(this)
                })
            }
            jq(".proopc-creditcard-info").each(function() {
                if (jq(this).parent(".vmpayment_cardinfo").length == 0) {
                    var e = jq(this).prevAll('input[name="virtuemart_paymentmethod_id"]');
                    var t = jq(e).attr("id");
                    var n = jq(e).data("pmtype");
                    var r = "hide";
                    if (jq("#" + t).is(":checked")) {
                        r = "show"
                    }
                    jq(this).wrap('<span class="vmpayment_cardinfo additional-payment-info ' + n + " " + r + '">')
                }
            });
            if (typeof klarna === "undefined") {
                klarnaloadneeded = false
            } else {
                klarnaloadneeded = true
            }
            if (klarnaloadneeded) {
                ProOPC.callKlarna()
            }
            jq("form#proopc-shipment-form").find("select").change(function() {
                jq.ajax({
                    dataType: "json",
                    url: window.URI,
                    data: "ctask=setshipments&" + jq("form#proopc-shipment-form").serialize(),
                    cache: false,
                    success: function(e) {
                        if (e.error) {
                            var t = '<div class="proopc-alert proopc-success-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + e.msg + "</div>";
                            jq("#proopc-system-message").html(t);
                            jq("html,body").animate({
                                scrollTop: jq("#proopc-system-message").offset().top - 100
                            }, 500)
                        } else {
                            ProOPC.getcartlist()
                        }
                    }
                })
            });
            jq("form#proopc-shipment-form").find("select").each(function() {
                jq(this).width(jq(this).parents("fieldset").width())
            });
            jq("form#proopc-payment-form").find("select").change(function() {
                if (jq('#proopc-payment-form input[type="radio"][name="virtuemart_paymentmethod_id"]:checked').data("pmtype") == "sisowideal" || jq('#proopc-payment-form input[type="radio"][name="virtuemart_paymentmethod_id"]:checked').data("pmtype") == "buckaroo") {
                    var e = jq("form#proopc-payment-form").serialize();
                    var t = "ctask=setpayment&savecc=1&payment_data=1";
                    if (jq('#proopc-payment-form input[type="radio"][name="virtuemart_paymentmethod_id"]:checked').data("pmtype") == "piraeus") {
                        t = "ctask=setpayment&payment_data=1"
                    }
                    jq.ajax({
                        dataType: "json",
                        url: window.URI,
                        data: t + "&" + e,
                        cache: false,
                        success: function(e) {
                            if (e.error) {
                                var t = '<div class="proopc-alert proopc-success-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + e.msg + "</div>";
                                jq("#proopc-system-message").html(t);
                                jq("html,body").animate({
                                    scrollTop: jq("#proopc-system-message").offset().top - 100
                                }, 500)
                            } else {
                                ProOPC.getcartlist()
                            }
                        }
                    })
                } else if (jq('#proopc-payment-form input[type="radio"][name="virtuemart_paymentmethod_id"]:checked').data("pmtype") == "piraeus") {
                    ProOPC.setpayment(jq('#proopc-payment-form input[type="radio"][name="virtuemart_paymentmethod_id"]:checked'))
                }
            });
            jq("#sisow_bank").width(jq("#sisow_bank").parent("fieldset").width());
            jq("#monthinstallments").width(jq("#monthinstallments").parents(".proopc-row").width());
            jq(".cc_type_sandbox").change(function() {
                var e = jq(this).attr("rel");
                var t = jq("#cc_type_" + e).val();
                switch (t) {
                    case "Visa":
                        jq("#cc_number_" + e).val("4007000000027");
                        jq("#cc_cvv_" + e).val("123");
                        break;
                    case "Mastercard":
                        jq("#cc_number_" + e).val("6011000000000012");
                        jq("#cc_cvv_" + e).val("123");
                        break;
                    case "Amex":
                        jq("#cc_number_" + e).val("370000000000002");
                        jq("#cc_cvv_" + e).val("1234");
                        break;
                    case "Discover":
                        jq("#cc_number_" + e).val("5424000000000015");
                        jq("#cc_cvv_" + e).val("123");
                        break;
                    case "Maestro":
                        jq("#cc_number_" + e).val("6763318282526706");
                        jq("#cc_cvv_" + e).val("123");
                        break;
                    default:
                        jq("#cc_number_" + e).val("");
                        jq("#cc_cvv_" + e).val("")
                }
            });
            jq(".cc_type_sandbox").trigger("change");
            var i = jq(".vmpayment_cardinfo");
            if (i.parent().is("div")) {
                i.unwrap()
            }
            if (window.REMOVEUNNECESSARYLINKS == 1) {
                jq("span.vmpayment").find("a").each(function() {
                    var e = jq(this).text();
                    jq(this).parents("label").siblings(".vmpayment_cardinfo").prepend('<div class="proopc-payment-text">' + e + "</div>");
                    jq(this).remove()
                });
                jq("#ProOPC").find('a[href="' + window.EDITPAYMENTURI + '"]').remove()
            }
            if (window.TOSFANCY) {
                jq('[data-tos="fancybox"]').fancybox({
                    titlePosition: "inside",
                    padding: 0,
                    showCloseButton: false,
                    centerOnScroll: true,
                    transitionIn: "fade",
                    transitionOut: "elastic",
                    overlayOpacity: .8,
                    overlayColor: "#000",
                    onStart: function() {
                        jq("#header .navigation.sticky").css("z-index", 1e3)
                    },
                    onClosed: function() {
                        jq("#header .navigation.sticky").removeAttr("style")
                    }
                });
                jq("button.fancy-close").click(function(e) {
                    e.preventDefault();
                    parent.jq.fancybox.close()
                })
            }
            if (window.RELOAD && !jq("#ProOPC").hasClass("loaded") && jq(".proopc-reload").length) {
                jq("html,body").animate({
                    scrollTop: jq("#proopc-system-message").offset().top - 100
                }, 500, function() {
                    jq("#ProOPC").addClass("loaded")
                })
            }
        },
        close: function(e) {
            jq(e).parent(".proopc-alert").remove()
        },
        validateForm: function(e) {
            var t = "";
            var n = true;
            jq(e + ' input[type="text"],' + e + ' input[type="password"],' + e + " input.required," + e + " select.required").each(function() {
                jq('label[for="' + jq(this).attr("id") + '"]').removeClass("invalid");
                if (jq(this).val() == "" || jq(this).hasClass("invalid")) {
                    jq(this).siblings(".status").removeClass("valid").addClass("invalid").attr("title", Joomla.JText._("PLG_VPONEPAGECHECKOUT_REQUIRED_FIELD"));
                    t = Joomla.JText._("PLG_VPONEPAGECHECKOUT_REQUIRED_FIELDS_MISSING");
                    n = false
                } else if (jq(this).attr("type") == "checkbox" && jq(this).is(":checked") == false) {
                    jq('label[for="' + jq(this).attr("id") + '"]').addClass("invalid");
                    t = Joomla.JText._("PLG_VPONEPAGECHECKOUT_REQUIRED_FIELDS_MISSING");
                    n = false
                } else if (jq(this).attr("type") == "radio" && !jq('input[name="' + jq(this).attr("name") + '"]:checked').val()) {
                    jq('label[for="' + jq(this).attr("id") + '"]').addClass("invalid");
                    t = Joomla.JText._("PLG_VPONEPAGECHECKOUT_REQUIRED_FIELDS_MISSING");
                    n = false
                }
            });
            if (n == false) {
                var r = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + t + "</div>";
                jq("#proopc-system-message").html(r);
                jq("html,body").animate({
                    scrollTop: jq("#proopc-system-message").offset().top - 100
                }, 500)
            }
            return n
        },
        submitOrder: function() {
            jq("#proopc-system-message").html("");
            var e = false;
            if (jq("#virtuemart_state_id optgroup").length > 0 && jq("#virtuemart_state_id").val() == "") {
                jq("#virtuemart_state_id").addClass("required")
            }
            if (jq("#shipto_virtuemart_state_id optgroup").length > 0 && jq("#shipto_virtuemart_state_id").val() == "") {
                jq("#shipto_virtuemart_state_id").addClass("required")
            }
            jq("#EditBTAddres select, #EditBTAddres input").each(function() {
                if (jq(this).hasClass("required") && jq(this).val() == "") {
                    jq(this).addClass("invalid");
                    var t = jq(this).attr("id");
                    jq('label[for="' + t + '"]').addClass("invalid");
                    e = true
                }
            });
            jq("#EditBTAddres select, #EditBTAddres input").change(function() {
                if (jq(this).hasClass("invalid") && jq(this).val() !== "") {
                    jq(this).removeClass("invalid");
                    var e = jq(this).attr("id");
                    jq('label[for="' + e + '"]').removeClass("invalid")
                }
            });
            if (jq("#STsameAsBT").is(":checked")) {
                jq("#EditBTAddres input").each(function() {
                    var e = jq(this).attr("id");
                    jq("#shipto_" + e).val(jq(this).val())
                })
            } else {
                jq("#EditSTAddres select, #EditSTAddres input").each(function() {
                    if (jq(this).hasClass("required") && jq(this).val() == "") {
                        jq(this).addClass("invalid");
                        var t = jq(this).attr("id");
                        jq('label[for="' + t + '"]').addClass("invalid");
                        e = true
                    }
                });
                jq("#EditSTAddres select, #EditSTAddres input").change(function() {
                    if (jq(this).hasClass("invalid") && jq(this).val() !== "") {
                        jq(this).removeClass("invalid");
                        var e = jq(this).attr("id");
                        jq('label[for="' + e + '"]').removeClass("invalid")
                    }
                })
            }
            if (e) {
                var t = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + Joomla.JText._("COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS") + "</div>";
                jq("#proopc-system-message").html(t);
                jq("html,body").animate({
                    scrollTop: jq("#proopc-system-message").offset().top - 100
                }, 500);
                return false
            }
            if (jq('#proopc-shipments input[name="virtuemart_shipmentmethod_id"]').is(":checked") == false) {
                var t = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + Joomla.JText._("COM_VIRTUEMART_CART_NO_SHIPMENT_SELECTED") + "</div>";
                jq("#proopc-system-message").html(t);
                jq("html,body").animate({
                    scrollTop: jq("#proopc-system-message").offset().top - 100
                }, 500);
                return false
            }
            if (jq('#proopc-payments input[name="virtuemart_paymentmethod_id"]').is(":checked") == false) {
                var t = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + Joomla.JText._("COM_VIRTUEMART_CART_NO_PAYMENT_SELECTED") + "</div>";
                jq("#proopc-system-message").html(t);
                jq("html,body").animate({
                    scrollTop: jq("#proopc-system-message").offset().top - 100
                }, 500);
                return false
            }
            var n = jq("#proopc-payment-form").find("input:radio[name=virtuemart_paymentmethod_id]:checked");
            if (n.hasClass("klarnaPayment")) {
                var r = false;
                var i = [];
                var s = n.parents("table");
                jq(s).find("input, select, textarea, .klarna_box_bottom_title").removeClass("invalid");
                jq(s).find("input").not(":checkbox").each(function() {
                    if (jq(this).val() == "") {
                        jq(this).addClass("invalid");
                        jq(this).prev(".klarna_box_bottom_title").addClass("invalid");
                        i.push(jq(this).prev(".klarna_box_bottom_title").text());
                        r = true
                    }
                });
                var o = false;
                jq(s).find("select").each(function() {
                    if (parseInt(jq(this).val()) == "" || parseInt(jq(this).val()) == 0 || isNaN(parseInt(jq(this).val()))) {
                        jq(this).addClass("invalid");
                        jq(this).parents(".klarna_box_bottom_input_combo").prev(".klarna_box_bottom_title").addClass("invalid");
                        if (o == false) {
                            i.push(jq(this).parents(".klarna_box_bottom_input_combo").prev(".klarna_box_bottom_title").text());
                            o = true
                        }
                        r = true
                    }
                });
                jq(s).find('input[type="checkbox"]').each(function() {
                    if (jq(this).is(":checked") == false) {
                        jq(this).addClass("invalid");
                        jq(this).next(".klarna_box_bottom_title").addClass("invalid");
                        i.push("Klarna: " + Joomla.JText._("COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS"));
                        r = true
                    }
                });
                var u = "";
                if (jq(i).length > 0) {
                    u = Joomla.JText._("COM_VIRTUEMART_USER_FORM_MISSING_REQUIRED_JS") + ": " + i.join(", ") + "."
                }
                jq(s).find("input, select, textarea").change(function() {
                    if (jq(this).is("input") && jq(this).val() != "") {
                        jq(this).removeClass("invalid");
                        jq(this).prev(".klarna_box_bottom_title").removeClass("invalid")
                    }
                    if (jq(this).is("select") && (jq(this).val() != "" || jq(this).val() != "0")) {
                        jq(this).removeClass("invalid");
                        jq(this).parents(".klarna_box_bottom_input_combo").prev(".klarna_box_bottom_title").removeClass("invalid")
                    }
                    if (jq(this).is("input:checkbox, input:radio") && jq(this).is(":checked")) {
                        jq(this).removeClass("invalid");
                        jq(this).next(".klarna_box_bottom_title").removeClass("invalid")
                    }
                })
            }
            if (r) {
                var t = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + u + "</div>";
                jq("#proopc-system-message").html(t);
                jq("html,body").animate({
                    scrollTop: jq("#proopc-system-message").offset().top - 100
                }, 500);
                return false
            }
            if (window.VMCONFIGTOS) {
                if (jq("#tosAccepted").is(":checked") == false) {
                    var t = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + Joomla.JText._("COM_VIRTUEMART_CART_PLEASE_ACCEPT_TOS") + "</div>";
                    jq("#proopc-system-message").html(t);
                    jq("html,body").animate({
                        scrollTop: jq("#proopc-system-message").offset().top - 100
                    }, 500);
                    return false
                }
            }
            var a = jq("#EditBTAddres").serialize();
            var f = jq("#checkoutForm").serialize();
            var l = jq("#formToken input").attr("name");
            var c = jq("#formToken input").val();
            var h = a + "&" + f + "&option=com_virtuemart&task=saveUser&controller=user&ctask=savebtaddress&stage=final&" + l + "=" + c;
            jq.ajax({
                beforeSend: function() {
                    ProOPC.addpageloader();
                    jq("#proopc-page-spinner").after('<div id="proopc-order-process"></div>');
                    jq("#proopc-order-process").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_SAVING_BILLING_ADDRESS"));
                    jq("#proopc-order-submit").attr("disabled", "disabled")
                },
                dataType: "json",
                type: "POST",
                url: window.URI,
                data: h,
                success: function(e) {
                    if (e.error == 1) {
                        ProOPC.removepageloader();
                        var t = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + e.msg + "</div>";
                        jq("#proopc-system-message").html(t);
                        jq("html,body").animate({
                            scrollTop: jq("#proopc-system-message").offset().top - 100
                        }, 500);
                        jq("#proopc-order-submit").removeAttr("disabled");
                        return false
                    }
                    jq("#proopc-order-process").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_BILLING_ADDRESS_SAVED"));
                    if (jq("#STsameAsBT").is(":checked") == false) {
                        var n = jq("#EditSTAddres").serialize();
                        var r = jq("#formToken input").attr("name");
                        var i = jq("#formToken input").val();
                        var s = n + "&option=com_virtuemart&task=saveUser&controller=user&ctask=savestaddress&stage=final&" + r + "=" + i;
                        jq.ajax({
                            beforeSend: function() {
                                jq("#proopc-order-process").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_SAVING_SHIPPING_ADDRESS"))
                            },
                            dataType: "json",
                            type: "POST",
                            async: false,
                            url: window.URI,
                            data: s,
                            success: function(e) {
                                if (e.error == 1) {
                                    ProOPC.removepageloader();
                                    var t = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + e.msg + "</div>";
                                    jq("#proopc-system-message").html(t);
                                    jq("html,body").animate({
                                        scrollTop: jq("#proopc-system-message").offset().top - 100
                                    }, 500);
                                    jq("#proopc-order-submit").removeAttr("disabled");
                                    return false
                                }
                                jq("#proopc-order-process").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_SHIPPING_ADDRESS_SAVED"));
                                ProOPC.saveCCdata()
                            }
                        })
                    } else {
                        ProOPC.saveCCdata()
                    }
                }
            });
            return false
        },
        saveCCdata: function() {
            var e = jq("form#proopc-payment-form").serialize();
            var t = jq("#proopc-payment-form").find("input:radio[name=virtuemart_paymentmethod_id]:checked");
            if (t.hasClass("klarnaPayment")) {
                var e = ProOPC.getKlarnaForm().serialize()
            }
            jq.ajax({
                beforeSend: function() {
                    jq("#proopc-order-process").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_SAVING_CREDIT_CARD"))
                },
                dataType: "json",
                type: "POST",
                url: window.URI,
                async: false,
                data: "ctask=setpayment&ajax=1&savecc=1&" + e + "&payment_data=1",
                cache: false,
                success: function(e, t, n) {
                    if (n.getResponseHeader("content-type").indexOf("text/html") >= 0 && typeof klarna !== "undefined") {
                        ProOPC.removepageloader();
                        jq("#proopc-order-process").remove();
                        jq("<div/>", {
                            id: "proopc-temp",
                            style: "display:none"
                        }).appendTo("body");
                        jq("#proopc-temp").append(e);
                        var r = jq("#proopc-temp").find("div#system-message-container").html();
                        var i = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + r + "</div>";
                        jq("#proopc-system-message").html(i);
                        jq("html,body").animate({
                            scrollTop: jq("#proopc-system-message").offset().top - 100
                        }, 500);
                        jq("#proopc-order-submit").removeAttr("disabled");
                        return false
                    } else {
                        if (typeof e === "string") {
                            e = jq.parseJSON(e)
                        }
                        if (e.error) {
                            ProOPC.getcartlist();
                            ProOPC.removepageloader();
                            jq("#proopc-order-process").remove();
                            var i = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + e.msg + "</div>";
                            jq("#proopc-system-message").html(i);
                            jq("html,body").animate({
                                scrollTop: jq("#proopc-system-message").offset().top - 100
                            }, 500);
                            jq("#proopc-order-submit").removeAttr("disabled");
                            return false
                        } else if (e.redirect != false) {
                            window.location = e.redirect
                        } else {
                            jq("#proopc-order-process").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_CREDIT_CARD_SAVED"));
                            ProOPC.verifyCheckout();
                            return true
                        }
                    }
                }
            })
        },
        verifyCheckout: function() {
            jq.ajax({
                beforeSend: function() {
                    jq("#proopc-order-process").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_VERIFYING_ORDER"))
                },
                dataType: "json",
                type: "POST",
                async: false,
                url: window.URI,
                data: "ctask=verifycheckout",
                success: function(e) {
                    if (e.error == 0) {
                        jq("#proopc-order-process").text(Joomla.JText._("PLG_VPONEPAGECHECKOUT_PLACING_ORDER"));
                        jq("#checkoutForm").submit()
                    } else {
                        ProOPC.removepageloader();
                        jq("#proopc-order-process").remove();
                        var t = '<div class="proopc-alert proopc-error-msg"><button type="button" class="close" onclick="ProOPC.close(this);">x</button>' + e.msg + "</div>";
                        jq("#proopc-system-message").html(t);
                        jq("html,body").animate({
                            scrollTop: jq("#proopc-system-message").offset().top - 100
                        }, 500);
                        jq("#proopc-order-submit").removeAttr("disabled");
                        return false
                    }
                }
            })
        },
        callKlarna: function() {
            var e = jq("#klarna_baloon").clone();
            jq("body").find(".klarna_baloon").remove();
            var t = jq("#klarna_blue_baloon").clone();
            jq("body").find(".klarna_blue_baloon").remove();
            jq("body").append(e);
            jq("body").append(t);
            klarna.doDocumentIsReady(jq(".klarna_box"));
            jq(".klarna_box_bottom_languageInfo").remove();
            if (!klarna.unary_checkout) {
                var n = jq("#proopc-payment-form input[type=radio][name=virtuemart_paymentmethod_id]");
                ProOPC.initPaymentSelection(n.filter(":checked"))
            }
            klarna.baloons_moved = true
        },
        initPaymentSelection: function(e) {
            var t = e.hasClass("klarnaPayment");
            klarna.gChoice = "";
            klarna.stype = e.data("stype");
            if (t) {
                klarna.gChoice = e.attr("id")
            }
            var n = jQuery("#proopc-payment-form .klarnaPayment");
            ProOPC.klarnaOpenClose(e)
        },
        klarnaOpenClose: function(e) {
            var t = jq(e).hasClass("klarnaPayment");
            if (t) {
                jq(e).parents(".proopc-klarna-payment").siblings().find(".klarna_box_bottom:visible").hide();
                jq(e).parents(".proopc-klarna-payment").find(".klarna_box_bottom:hidden").css("opacity", 0).show();
                jq(".proopc-klarna-payment").find('input[type="text"]').width("auto");
                jq(".proopc-klarna-payment").find('input[type="text"]').each(function() {
                    jq(this).outerWidth(jq(this).parent("div").width() - 3)
                });
                jq(e).parents(".proopc-klarna-payment").find("div.klarna_box_bottom_title:visible, div.klarna_box_bottom_title:visible").removeAttr("style");
                jq(e).parents(".proopc-klarna-payment").find("#box_klarna_consent_invoice:visible").parent("div").addClass("proopc-klarna-consent-container").width("auto");
                jq(e).parents(".proopc-klarna-payment").find("#box_klarna_consent_part:visible").parent("div").addClass("proopc-klarna-consent-container").width("auto");
                jq(e).parents(".proopc-klarna-payment").find(".klarna_box_bottom:visible").animate({
                    opacity: 1
                }, 300)
            }
        },
        getKlarnaForm: function() {
            var e = jq("#proopc-payment-form").find("input:radio[name=virtuemart_paymentmethod_id]:checked");
            if (!e.hasClass("klarnaPayment")) {
                return
            }
            var t = e.parents("table");
            var n = t.find("*").serializeArray();
            n.push({
                name: "task",
                value: "setpayment"
            });
            n.push({
                name: "view",
                value: "cart"
            });
            n.push({
                name: "klarna_paymentmethod",
                value: e.next("input").val()
            });
            var r = jq("<form></form>");
            jq.each(n, function(e, t) {
                var n = jq("<input></input>");
                n.attr("type", "hidden");
                n.attr("name", t.name);
                n.attr("value", t.value);
                r.append(n)
            });
            return r
        },
        loadPaymentScripts: function() {
            ProOPC.addloader("#proopc-payments");
            jq.ajax({
                dataType: "json",
                url: window.URI,
                data: "ctask=getpaymentscripts",
                cache: false,
                success: function(e) {
                    jq("#proopc-payments").html(e.payments);
                    if (e.payment_scripts[0] !== "") {
                        payment_scripts = e.payment_scripts
                    }
                    if (e.payment_script[0] !== "") {
                        jq.each(e.payment_script, function(e, t) {
                            jq.getScript(t, function(e, t, n) {
                                if (typeof payment_scripts !== "undefined") {
                                    jq.each(payment_scripts, function(e, t) {
                                        t
                                    })
                                }
                            })
                        })
                    }
                },
                complete: function() {
                    ProOPC.style();
                    ProOPC.tooltip();
                    ProOPC.removeloader("#proopc-payments");
                    ProOPC.defaultSP()
                },
                error: function() {
                    console.log("Error: Error gettings Payment Scripts.")
                }
            })
        },
        loadShipmentScripts: function() {
            if (jq("#proopc-shipment-form").find("select").length <= 0) {
                return
            }
            ProOPC.addloader("#proopc-shipments");
            jq.ajax({
                dataType: "json",
                url: window.URI,
                data: "ctask=getshipmentscripts",
                cache: false,
                success: function(e) {
                    jq("#proopc-shipments").html(e.shipments);
                    if (e.shipment_scripts.length > 0 && e.shipment_scripts[0] !== "") {
                        jq.each(e.shipment_scripts, function(e, t) {
                            jq("head, body").find('script[type="text/javascript"]').each(function() {
                                var e = jq(this).attr("src");
                                if (typeof e === "undefined" || e === false) {
                                    jq(this).append(t);
                                    return false
                                }
                            })
                        })
                    }
                },
                complete: function() {
                    ProOPC.style();
                    ProOPC.tooltip();
                    ProOPC.removeloader("#proopc-shipments");
                    ProOPC.defaultSP()
                },
                error: function() {
                    console.log("Error: Error gettings Shipment Scripts.")
                }
            })
        },
        getUrlVars: function() {
            var e = {},
                t, n;
            var r = ["option", "view", "task", "ctask"];
            var i = window.location.href.slice(window.location.href.indexOf("?") + 1).split("&");
            for (var s = 0; s < i.length; s++) {
                t = i[s].split("=");
                if (jq.inArray(t[0], r) == -1 && typeof t[1] !== "undefined" && t[1] != null) {
                    e[t[0]] = t[1]
                }
            }
            return jq.param(e)
        }
    };
    jq.xhrPool = [];
    jq.xhrPool.abortAll = function() {
        jq(this).each(function(e, t) {
            t.abort()
        });
        jq.xhrPool.length = 0
    };
    jq.emailPool = [];
    jq.emailPool.abortAll = function() {
        jq(this).each(function(e, t) {
            t.abort()
        });
        jq.emailPool.length = 0
    };
    jq.userPool = [];
    jq.userPool.abortAll = function() {
        jq(this).each(function(e, t) {
            t.abort()
        });
        jq.userPool.length = 0
    };
    jq(document).ready(function() {
        ProOPC.spinnervars();
        ProOPC.verifyRegForm();
        ProOPC.tooltip();
        ProOPC.inputwidth();
        ProOPC.selectwidth();
        ProOPC.defaultSP();
        ProOPC.productdetails()
    });
    jq(document).ajaxStop(function() {
        ProOPC.productdetails()
    });
    jq(window).load(function() {
        ProOPC.style()
    });
    jq(window).resize(function() {
        var e = navigator.userAgent.toLowerCase();
        var t = e.indexOf("android") > -1;
        if (!t) {
            ProOPC.style();
            ProOPC.inputwidth();
            ProOPC.selectwidth()
        }
    })
}