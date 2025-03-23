<div class="w-4/5 mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <form class="w-full space-y-4" id="addPropertyForm" onsubmit="handleAddProperty(event)" enctype="multipart/form-data">
            <!-- Management -->
            <?php include_once('views/components/add-property/management.php'); ?>
            <!-- Specifications -->
            <?php include_once('views/components/add-property/specifications.php'); ?>
            <!-- Property Permit -->
            <?php include_once('views/components/add-property/permit.php'); ?>
            <!-- Pricing -->
            <?php include_once('views/components/add-property/pricing.php'); ?>
            <!-- Title and Description -->
            <?php include_once('views/components/add-property/title.php'); ?>
            <!-- Amenities -->
            <?php include_once('views/components/add-property/amenities.php'); ?>
            <!-- Location -->
            <?php include_once('views/components/add-property/location.php'); ?>
            <!-- Photos and Videos -->
            <?php include_once('views/components/add-property/media.php'); ?>
            <!-- Floor Plan -->
            <?php include_once('views/components/add-property/floorplan.php'); ?>
            <!-- Documents -->
            <?php // include_once('views/components/add-property/documents.php'); 
            ?>
            <!-- Notes -->
            <?php include_once('views/components/add-property/notes.php'); ?>
            <!-- Portals -->
            <?php include_once('views/components/add-property/portals.php'); ?>
            <!-- Status -->
            <?php include_once('views/components/add-property/status.php'); ?>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="button" onclick="javascript:history.back()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-1">
                    Back
                </button>
                <button type="submit" id="submitButton" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById("offering_type").addEventListener("change", function() {
        const offeringType = this.value;
        console.log(offeringType);

        if (offeringType == 'RR' || offeringType == 'CR') {
            document.getElementById("rental_period").setAttribute("required", true);
            document.querySelector('label[for="rental_period"]').innerHTML = 'Rental Period (if rental) <span class="text-danger">*</span>';
        } else {
            document.getElementById("rental_period").removeAttribute("required");
            document.querySelector('label[for="rental_period"]').innerHTML = 'Rental Period (if rental)';
        }
    })

    async function addItem(entityTypeId, fields) {
        try {
            const response = await fetch(`${API_BASE_URL}crm.item.add?entityTypeId=${entityTypeId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    fields,
                }),
            });

            if (response.ok) {
                window.location.href = 'index.php?page=properties';
            } else {
                console.error('Failed to add item');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function handleAddProperty(e) {
        e.preventDefault();

        document.getElementById('submitButton').disabled = true;
        document.getElementById('submitButton').innerHTML = 'Submitting...';

        const form = document.getElementById('addPropertyForm');
        const formData = new FormData(form);
        const data = {};

        formData.forEach((value, key) => {
            data[key] = typeof value === 'string' ? value.trim() : value;
        });

        const agent = await getAgent(data.listing_agent);

        const fields = {
            "ufCrm12TitleDeed": data.title_deed,
            "ufCrm12ReferenceNumber": data.reference,
            "ufCrm12OfferingType": data.offering_type,
            "ufCrm12PropertyType": data.property_type,
            "ufCrm12Price": data.price,
            "ufCrm12TitleEn": data.title_en,
            "ufCrm12DescriptionEn": data.description_en,
            "ufCrm12TitleAr": data.title_ar,
            "ufCrm12DescriptionAr": data.description_ar,
            "ufCrm12Size": data.size,
            "ufCrm12Bedroom": data.bedrooms,
            "ufCrm12Bathroom": data.bathrooms,
            "ufCrm12Parking": data.parkings,
            "ufCrm12Geopoints": `${data.latitude}, ${data.longitude}`,
            "ufCrm12PermitNumber": data.dtcm_permit_number,
            "ufCrm12RentalPeriod": data.rental_period,
            "ufCrm12Furnished": data.furnished,
            "ufCrm12TotalPlotSize": data.total_plot_size,
            "ufCrm12LotSize": data.lot_size,
            "ufCrm12BuildupArea": data.buildup_area,
            "ufCrm12LayoutType": data.layout_type,
            "ufCrm12ProjectName": data.project_name,
            "ufCrm12ProjectStatus": data.project_status,
            "ufCrm12Ownership": data.ownership,
            "ufCrm12Developers": data.developer,
            "ufCrm12BuildYear": data.build_year,
            "ufCrm12Availability": data.availability,
            "ufCrm12AvailableFrom": data.available_from,
            "ufCrm12PaymentMethod": data.payment_method,
            "ufCrm12DownPaymentPrice": data.downpayment_price,
            "ufCrm12NoOfCheques": data.cheques,
            "ufCrm12ServiceCharge": data.service_charge,
            "ufCrm12FinancialStatus": data.financial_status,
            "ufCrm12VideoTourUrl": data.video_tour_url,
            "ufCrm_12_360_VIEW_URL": data["360_view_url"],
            "ufCrm12QrCodePropertyBooster": data.qr_code_url,
            "ufCrm12Location": data.pf_location,
            "ufCrm12City": data.pf_city,
            "ufCrm12Community": data.pf_community,
            "ufCrm12SubCommunity": data.pf_subcommunity,
            "ufCrm12Tower": data.pf_building,
            "ufCrm12BayutLocation": data.bayut_location,
            "ufCrm12BayutCity": data.bayut_city,
            "ufCrm12BayutCommunity": data.bayut_community,
            "ufCrm12BayutSubCommunity": data.bayut_subcommunity,
            "ufCrm12BayutTower": data.bayut_building,
            "ufCrm12Status": data.status,
            "ufCrm12ReraPermitNumber": data.rera_permit_number,
            "ufCrm12ReraPermitIssueDate": data.rera_issue_date,
            "ufCrm12ReraPermitExpirationDate": data.rera_expiration_date,
            "ufCrm12DtcmPermitNumber": data.dtcm_permit_number,
            "ufCrm12ListingOwner": data.listing_owner,
            // Landlord 1
            "ufCrm12LandlordName": data.landlord_name,
            "ufCrm12LandlordEmail": data.landlord_email,
            "ufCrm12LandlordContact": data.landlord_phone,
            // Landlord 2
            "ufCrm_12_LANDLORD_NAME_2": data.landlord_name2,
            "ufCrm_12_LANDLORD_EMAIL_2": data.landlord_email2,
            "ufCrm_12_LANDLORD_CONTACT_2": data.landlord_phone2,
            // Landlord 3
            "ufCrm_12_LANDLORD_NAME_3": data.landlord_name3,
            "ufCrm_12_LANDLORD_EMAIL_3": data.landlord_email3,
            "ufCrm_12_LANDLORD_CONTACT_3": data.landlord_phone3,

            "ufCrm12ContractExpiryDate": data.contract_expiry,
            "ufCrm12UnitNo": data.unit_no,
            "ufCrm12SaleType": data.sale_type,
            "ufCrm12BrochureDescription": data.brochure_description_1,
            "ufCrm_12_BROCHURE_DESCRIPTION_2": data.brochure_description_2,
            "ufCrm12HidePrice": data.hide_price == "on" ? "Y" : "N",
            "ufCrm12PfEnable": data.pf_enable == "on" ? "Y" : "N",
            "ufCrm12BayutEnable": data.bayut_enable == "on" ? "Y" : "N",
            "ufCrm12DubizzleEnable": data.dubizzle_enable == "on" ? "Y" : "N",
            "ufCrm12WebsiteEnable": data.website_enable == "on" ? "Y" : "N",
            "ufCrm12Watermark": data.watermark == "on" ? "Y" : "N",
        };

        if (agent) {
            fields["ufCrm12AgentId"] = agent.ufCrm12AgentId;
            fields["ufCrm12AgentName"] = agent.ufCrm12AgentName;
            fields["ufCrm12AgentEmail"] = agent.ufCrm12AgentEmail;
            fields["ufCrm12AgentPhone"] = agent.ufCrm12AgentMobile;
            fields["ufCrm12AgentPhoto"] = agent.ufCrm12AgentPhoto;
            fields["ufCrm12AgentLicense"] = agent.ufCrm12AgentLicense;
        }

        // Notes
        const notesString = data.notes;
        if (notesString) {
            const notesArray = JSON.parse(notesString);
            if (notesArray) {
                fields["ufCrm12Notes"] = notesArray;
            }
        }

        // Amenities
        const amenitiesString = data.amenities;
        if (amenitiesString) {
            const amenitiesArray = JSON.parse(amenitiesString);
            if (amenitiesArray) {
                fields["ufCrm12Amenities"] = amenitiesArray;
            }
        }

        // Property Photos
        const photos = document.getElementById('selectedImages').value;
        if (photos) {
            const fixedPhotos = photos.replace(/\\'/g, '"');
            const photoArray = JSON.parse(fixedPhotos);
            const watermarkPath = 'assets/images/watermark.png?cache=' + Date.now();
            const uploadedImages = await processBase64Images(photoArray, watermarkPath, data.watermark === "on");

            if (uploadedImages.length > 0) {
                fields["ufCrm12PhotoLinks"] = uploadedImages;
            }
        }

        // Floorplan
        const floorplan = document.getElementById('selectedFloorplan').value;
        if (floorplan) {
            const fixedFloorplan = floorplan.replace(/\\'/g, '"');
            const floorplanArray = JSON.parse(fixedFloorplan);
            const watermarkPath = 'assets/images/watermark.png?cache=' + Date.now();
            const uploadedFloorplan = await processBase64Images(floorplanArray, watermarkPath, data.watermark === "on");

            if (uploadedFloorplan.length > 0) {
                fields["ufCrm12FloorPlan"] = uploadedFloorplan[0];
            }
        }

        // Documents
        // const documents = document.getElementById('documents')?.files;
        // if (documents) {
        //     if (documents.length > 0) {
        //         let documentUrls = [];

        //         for (const document of documents) {
        //             if (document.size > 10485760) {
        //                 alert('File size must be less than 10MB');
        //                 return;
        //             }
        //             const uploadedDocument = await uploadFile(document);
        //             documentUrls.push(uploadedDocument);
        //         }

        //         fields["ufCrm12Documents"] = documentUrls;
        //     }

        // }

        // Add to CRM
        addItem(LISTINGS_ENTITY_TYPE_ID, fields, '?page=properties');
    }
</script>