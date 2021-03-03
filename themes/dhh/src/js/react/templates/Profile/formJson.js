export default [
  {
    title: "Personal",
    items: [
      {
        label: "Name in Passport",
        name: "full_name",
        type: "TextInput"
      },
      {
        label: "Birthdate",
        name: "dob",
        type: "BirthDate"
      },
      {
        name: "divider"
      },
      {
        label: "Email",
        name: "email",
        type: "TextInput"
      },
      {
        label: "Phone Number",
        name: "phone_number",
        type: "TextInput"
      },
      {
        name: "divider"
      },
      {
        label: "Passport Number",
        name: "passport_number",
        type: "TextInput",
        optional: true
      },
      {
        label: "Valid to",
        name: "valid_to",
        type: "DatePicker",
        optional: true
      }
    ]
  }
];
