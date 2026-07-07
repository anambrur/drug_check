<script>
    document.addEventListener('DOMContentLoaded', function() {
        const portfolioData = @json($portfolioData);
        const clientProfileData = @json($clientProfileData);
        const employeeData = @json($employeeData);
        const isEdit = @json($isEdit ?? false);
        const defaultLabAccount = @json(config('services.quest.lab_account'));
        const defaultDotLabAccount = @json(config('services.quest.dot_lab_account'));

        const portfolioSelect = document.getElementById('portfolio_select');
        const dotSelectionSection = document.getElementById('dot_selection_section');
        const clientProfileSelect = document.getElementById('client_profile_id');
        const employeeSelect = document.getElementById('employee_id');
        const dotTestSelect = document.getElementById('dot_test');
        const testingAuthorityWrap = document.getElementById('testing_authority_wrap');
        const testingAuthoritySelect = document.getElementById('testing_authority');
        const reasonWrap = document.getElementById('reason_for_test_wrap');
        const physicalReasonWrap = document.getElementById('physical_reason_wrap');
        const reasonSelect = document.getElementById('reason_for_test_id');
        const physicalReasonSelect = document.getElementById('physical_reason_for_test_id');
        const primaryIdTypeWrap = document.getElementById('primary_id_type_wrap');
        const zipCodeWrap = document.getElementById('zip_code_wrap');
        const ebatContactWrap = document.getElementById('ebat_contact_wrap');
        const ebatPhoneWrap = document.getElementById('ebat_phone_wrap');
        const contactNameInput = document.getElementById('contact_name');
        const telephoneInput = document.getElementById('telephone_number');
        const endDateTime = document.getElementById('end_datetime');

        let currentPortfolio = null;

        function setValue(id, value) {
            const el = document.getElementById(id);
            if (el) {
                el.value = value ?? '';
            }
        }

        function toggleTestingAuthority() {
            const isDot = dotTestSelect && dotTestSelect.value === 'T';
            if (testingAuthorityWrap) {
                testingAuthorityWrap.style.display = isDot ? 'block' : 'none';
            }
            if (testingAuthoritySelect) {
                testingAuthoritySelect.required = isDot;
            }
            if (dotSelectionSection) {
                dotSelectionSection.style.display = isDot ? '' : 'none';
            }
            if (employeeSelect) {
                employeeSelect.required = isDot && !isEdit;
            }
        }

        function applyPortfolioFlags(portfolio) {
            const isPhysical = portfolio ? portfolio.is_physical : false;
            const isEbat = portfolio ? portfolio.is_ebat : false;

            if (reasonWrap) {
                reasonWrap.style.display = isPhysical ? 'none' : 'block';
            }
            if (physicalReasonWrap) {
                physicalReasonWrap.style.display = isPhysical ? 'block' : 'none';
            }
            if (reasonSelect) {
                reasonSelect.required = !isPhysical;
                reasonSelect.disabled = isPhysical;
            }
            if (physicalReasonSelect) {
                physicalReasonSelect.required = isPhysical;
                physicalReasonSelect.disabled = !isPhysical;
            }
            if (primaryIdTypeWrap) {
                primaryIdTypeWrap.style.display = isPhysical ? 'block' : 'none';
            }
            if (zipCodeWrap) {
                zipCodeWrap.style.display = isPhysical ? 'block' : 'none';
            }
            if (ebatContactWrap) {
                ebatContactWrap.style.display = isEbat ? 'block' : 'none';
            }
            if (ebatPhoneWrap) {
                ebatPhoneWrap.style.display = isEbat ? 'block' : 'none';
            }
            document.querySelectorAll('.ebat-required-marker').forEach(function(el) {
                el.style.display = isEbat ? 'inline' : 'none';
            });
            if (contactNameInput) {
                contactNameInput.required = isEbat;
            }
            if (telephoneInput) {
                telephoneInput.required = isEbat;
            }
        }

        function applyLabAccount(portfolio) {
            if (!portfolio) {
                return;
            }

            if (portfolio.is_dot) {
                const clientId = clientProfileSelect ? clientProfileSelect.value : '';
                const profile = clientId ? clientProfileData[clientId] : null;
                setValue('lab_account', portfolio.lab_account || defaultDotLabAccount);
                return;
            }



            setValue('lab_account', portfolio.lab_account || defaultLabAccount);
        }

        function applyClientProfile(profileId) {
            const profile = profileId ? clientProfileData[profileId] : null;
            if (!profile) {
                if (currentPortfolio && currentPortfolio.is_dot) {
                    setValue('lab_account', defaultDotLabAccount || defaultLabAccount);
                }
                return;
            }

            if (currentPortfolio && currentPortfolio.is_dot) {
                setValue('lab_account', profile.account_no || defaultDotLabAccount || defaultLabAccount);
            }

            if (profile.testing_authority && testingAuthoritySelect) {
                const optionExists = Array.from(testingAuthoritySelect.options).some(function(opt) {
                    return opt.value === profile.testing_authority;
                });
                if (optionExists) {
                    testingAuthoritySelect.value = profile.testing_authority;
                }
            }

            if (currentPortfolio && currentPortfolio.is_ebat) {
                setValue('contact_name', profile.der_contact_name || '');
                setValue('telephone_number', (profile.der_contact_phone || '').replace(/\D/g, ''));
            }
        }

        function filterEmployees() {
            if (!employeeSelect) {
                return;
            }

            const clientId = clientProfileSelect ? clientProfileSelect.value : '';
            const currentValue = employeeSelect.value;

            Array.from(employeeSelect.options).forEach(function(option, index) {
                if (index === 0) {
                    option.hidden = false;
                    return;
                }

                const matches = !clientId || option.dataset.clientProfileId === clientId;
                option.hidden = !matches;
            });

            const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
            if (selectedOption && selectedOption.hidden) {
                employeeSelect.value = '';
            } else if (currentValue) {
                employeeSelect.value = currentValue;
            }
        }

        function applyEmployee(employeeId) {
            const data = employeeId ? employeeData[employeeId] : null;
            if (!data) {
                return;
            }

            setValue('first_name', data.first_name);
            setValue('last_name', data.last_name);
            setValue('middle_name', data.middle_name);
            setValue('primary_id', data.primary_id);
            setValue('email', data.email);
            setValue('primary_phone', data.primary_phone);
            setValue('dob', data.dob);

            if (data.client_profile_id && clientProfileSelect) {
                clientProfileSelect.value = data.client_profile_id;
                applyClientProfile(String(data.client_profile_id));
            }
        }

        function applyPortfolio(portfolioId) {
            const portfolio = portfolioId ? portfolioData[portfolioId] : null;
            currentPortfolio = portfolio;

            if (!portfolio) {
                setValue('portfolio_id', '');
                setValue('portfolio_name', '');
                setValue('unit_codes', '');
                applyPortfolioFlags(null);
                return;
            }

            setValue('portfolio_id', portfolio.id);
            setValue('portfolio_name', portfolio.title);
            setValue('unit_codes', portfolio.code || '');

            if (dotTestSelect) {
                dotTestSelect.value = portfolio.is_dot ? 'T' : 'F';
            }

            toggleTestingAuthority();
            applyPortfolioFlags(portfolio);
            applyLabAccount(portfolio);
            filterEmployees();
        }

        if (portfolioSelect) {
            portfolioSelect.addEventListener('change', function() {
                applyPortfolio(this.value);
            });
        }

        if (clientProfileSelect) {
            clientProfileSelect.addEventListener('change', function() {
                filterEmployees();
                applyClientProfile(this.value);
            });
        }

        if (employeeSelect) {
            employeeSelect.addEventListener('change', function() {
                applyEmployee(this.value);
            });
        }

        if (dotTestSelect) {
            dotTestSelect.addEventListener('change', toggleTestingAuthority);
        }

        document.querySelectorAll('.quest-phone-digits').forEach(function(el) {
            el.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '');
            });
        });

        if (endDateTime) {
            endDateTime.addEventListener('change', function() {
                if (!currentPortfolio || !currentPortfolio.is_physical) {
                    return;
                }
                const selected = new Date(this.value);
                const max = new Date(Date.now() + 168 * 3600 * 1000);
                if (selected > max) {
                    alert('For physical tests, the expiration date must be within 7 days.');
                    this.value = '';
                }
            });
        }

        if (typeof $ !== 'undefined' && $.fn.select2) {
            const siteSelect = $('.select2-collection-sites');
            @if (!empty($initialCollectionSite))
                const initialSite = @json($initialCollectionSite);
                if (initialSite && initialSite.id) {
                    const option = new Option(initialSite.text, initialSite.id, true, true);
                    siteSelect.append(option);
                }
            @endif

            siteSelect.select2({
                placeholder: 'Search collection sites…',
                allowClear: true,
                minimumInputLength: 2,
                width: '100%',
                ajax: {
                    url: '{{ route('collection-sites.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(site) {
                                return {
                                    id: site.collection_site_code,
                                    text: site.text
                                };
                            }),
                        };
                    },
                },
            });
        }

        if (portfolioSelect && portfolioSelect.value) {
            applyPortfolio(portfolioSelect.value);
        } else {
            toggleTestingAuthority();
            applyPortfolioFlags(null);
        }

        filterEmployees();

        if (employeeSelect && employeeSelect.value) {
            applyEmployee(employeeSelect.value);
        }
    });
</script>
