# PR Classes Website

A comprehensive website for PR Classes, offering CMA Inter and Final courses.

## Server Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

## Installation

1. Clone the repository to your web server
2. Create a MySQL database
3. Import the database schema from `database/schema.sql`
4. Copy `.env.example` to `.env` and update with your configuration
5. Ensure the `logs` directory is writable
6. Run `composer install` to install dependencies (development only)

## Production Deployment

1. Upload all files to your web server
2. Create a MySQL database on your hosting
3. Import the database schema
4. Update the database credentials in `includes/config.php`
5. Ensure all directories have proper permissions
6. Run the deployment script: `bash deploy.sh`

## Security Considerations

- Keep the `.env` file secure and outside the web root if possible
- Regularly update all dependencies
- Implement proper input validation for all user inputs
- Use prepared statements for all database queries
- Keep error display off in production

## Maintenance

- Regularly backup the database
- Monitor error logs in the `logs` directory
- Update dependencies when security patches are available

## License

All rights reserved. This code is proprietary and confidential.

This website serves as a platform for PR Classes to showcase their courses, student testimonials, and provide easy contact options for prospective students. The implementation uses PHP/MySQL for backend functionality with a clean, responsive frontend design.

## Features Checklist

### Frontend Features

#### 1. Home Page
- [x] Welcoming message with professional and modern UI
- [x] Responsive design optimized for mobile and desktop
- [x] Clear Call-to-Action (CTA) sections with 'Join Now' buttons
- [x] Enrollment status indicators

#### 2. About Page
- [x] Introduction to PR Classes and faculty
- [x] Mission & Vision statements

#### 3. Courses Page
- [x] Dropdown for CMA Inter & CMA Final courses
- [x] Course details with schedule, fees, and discount offers
- [x] 'Join Now' and 'Share to Friends' buttons for engagement

#### 4. Success Stories Page

##### Testimonials Section
- [ ] Display approved student testimonials dynamically
- [ ] "Add Your Testimonial" button prominently displayed on right side
- [ ] Testimonial submission form with:
  - Name
  - Registration no.
  - Subject (dropdown: CMA Inter. Cost Accounting / CMA Final Financial Management)
  - Course (dropdown: Comprehensive course / Crash course)
  - Mode (dropdown: Online class / Direct class)
  - Year
  - Mobile (for reference only, not displayed)
  - Testimonial content
  - Profile picture upload (optional, displayed as thumbnail)
  - Save button

##### Marksheets Section
- [ ] Display approved student marksheets
- [ ] "Upload Your Marksheet" button prominently displayed on right side
- [ ] Marksheet submission form with:
  - Name
  - Registration no.
  - Subject (dropdown: CMA Inter. Cost Accounting / CMA Final Financial Management)
  - Course (dropdown: Comprehensive course / Crash course)
  - Mode (dropdown: Online class / Direct class)
  - Year
  - Mobile (for reference only, not displayed)
  - Marksheet image upload
  - Profile picture upload (optional, displayed as thumbnail)
  - Save button

##### Feedback Videos Section
- [ ] Display embedded YouTube videos of student feedback
- [ ] "Upload Your Feedback Video" button prominently displayed on right side
- [ ] Video submission form with:
  - Name
  - Registration no.
  - Subject (dropdown: CMA Inter. Cost Accounting / CMA Final Financial Management)
  - Course (dropdown: Comprehensive course / Crash course)
  - Mode (dropdown: Online class / Direct class)
  - Year
  - Mobile (for reference only, not displayed)
  - Video instructions (Form submits info to admin via WhatsApp for YouTube processing)
  - Profile picture upload (optional, displayed as thumbnail)
  - Save button (generates WhatsApp message to admin)

#### 5. Gallery System
- [ ] Photos section displaying achievements and classroom moments
- [ ] Videos section with embedded YouTube players

#### 6. Contact & Inquiry Page
- [x] 'WhatsApp Inquiry' Button for direct communication
- [x] Callback Request Form with name, mobile, and query input
- [x] Form submission creates pre-filled WhatsApp message

### Backend Features (Admin Panel)

#### 1. User Management
- [ ] Secure admin login to manage website content

#### 2. Course Management
- [ ] Add/Edit/Delete courses
- [ ] Toggle enrollment status (Open/Closed)
- [ ] Modify discount offers dynamically

#### 3. Testimonials System
- [ ] Review pending testimonials with all submitted details
- [ ] Approve or reject testimonials before displaying them
- [ ] Manage existing approved testimonials

#### 4. Marksheet Upload System
- [ ] Review pending marksheet submissions with all details
- [ ] Approve or reject marksheets before displaying them
- [ ] Manage existing approved marksheets

#### 5. Feedback Video Upload System
- [ ] Receive student video submission details via WhatsApp
- [ ] Admin interface to add YouTube link for approved videos
- [ ] Link student information with YouTube videos
- [ ] Manage existing feedback videos

#### 6. Gallery System
- [ ] Upload student images
- [ ] Embed YouTube videos
- [ ] Manage existing gallery items

#### 7. Contact & Inquiry Management
- [ ] Handle WhatsApp inquiries and callback requests

#### 8. User Authentication and Authorization
- [ ] User authentication and authorization

#### 9. Database Integration
- [x] Database integration for course management

#### 10. API Integration
- [x] API integration for WhatsApp messaging

#### 11. Admin Dashboard
- [ ] Admin dashboard for monitoring student progress and course statistics

### Additional Features

#### 1. Gallery Page
- [ ] Gallery page to showcase student projects

#### 2. Contact Form
- [x] Contact form for inquiries and feedback

#### 3. Testimonials Section
- [ ] Testimonials section to display student feedback

### Future Enhancements

- [ ] Implement video conferencing for online classes
- [ ] Add payment gateway for online enrollment.

### Navigation & Headers
1. Home
2. About
3. Success Stories (Dropdown: Testimonials, Marksheets, Student Feedback Videos)
4. Courses (Dropdown: CMA Inter / CMA Final)
5. Class Videos
6. Contact

### Dynamic Features & Highlights
- [x] Discount Offer Management: Admin can update offers dynamically
- [x] Limited Seats & Urgency Messaging: Automated notifications for students
- [x] Enrollment Status: Easy toggle to open/close batch enrollments
- [x] WhatsApp-Based Inquiry System: Integrated system for student interaction

## Form Specifications

### Testimonial Submission Form
```
Name: [text field] *
Registration no.: [text field] *
Subject: [dropdown: CMA Inter. Cost Accounting / CMA Final Financial Management] *
Course: [dropdown: Comprehensive course / Crash course] *
Mode: [dropdown: Online class / Direct class] *
Year: [text field] *
Mobile: [text field] * (only for reference, will not be shared in website)
Testimonial: [textarea] *
Picture: [file upload - optional] (Will be displayed only as a small thumbnail pic)
[Save button]
```

### Marksheet Upload Form
```
Name: [text field] *
Registration no.: [text field] *
Subject: [dropdown: CMA Inter. Cost Accounting / CMA Final Financial Management] *
Course: [dropdown: Comprehensive course / Crash course] *
Mode: [dropdown: Online class / Direct class] *
Year: [text field] *
Mobile: [text field] * (only for reference, will not be shared in website)
Marksheet: [file upload] *
Picture: [file upload - optional] (Will be displayed only as a small thumbnail pic)
[Save button]
```

### Feedback Video Submission Form
```
Name: [text field] *
Registration no.: [text field] *
Subject: [dropdown: CMA Inter. Cost Accounting / CMA Final Financial Management] *
Course: [dropdown: Comprehensive course / Crash course] *
Mode: [dropdown: Online class / Direct class] *
Year: [text field] *
Mobile: [text field] * (only for reference, will not be shared in website)
Video: [Instructions for WhatsApp submission]
Picture: [file upload - optional] (Will be displayed only as a small thumbnail pic)
[Save button (generates WhatsApp message)]
```

## Technical Implementation

### Database Structure

1. **Users Table**
   - id (PK)
   - username
   - password_hash

2. **Courses Table**
   - id (PK)
   - title
   - description
   - category (CMA Inter/Final)
   - fees
   - discount_percentage
   - schedule
   - enrollment_status (Open/Closed)
   - limited_seats
   - seats_available
   - created_at
   - updated_at

3. **Testimonials Table**
   - id (PK)
   - name
   - registration_no
   - subject
   - course
   - mode
   - year
   - mobile
   - content
   - image_path
   - status (Pending/Approved/Rejected)
   - created_at

4. **Marksheets Table**
   - id (PK)
   - name
   - registration_no
   - subject
   - course
   - mode
   - year
   - mobile
   - marksheet_path
   - image_path
   - status (Pending/Approved/Rejected)
   - created_at

5. **Videos Table**
   - id (PK)
   - name
   - registration_no
   - subject
   - course
   - mode
   - year
   - mobile
   - youtube_url
   - image_path
   - status (Pending/Approved/Rejected)
   - created_at

6. **Gallery Table**
   - id (PK)
   - title
   - description
   - type (image/video)
   - path (file path or YouTube URL)
   - created_at

## Development Phases

### Phase 1: Setup & Basic Structure
- [ ] Set up hosting account with PHP/MySQL
- [ ] Create database and tables
- [ ] Implement basic project structure

### Phase 2: Backend Development
- [ ] Develop admin login system
- [ ] Implement course management
- [ ] Create testimonial and marksheet approval systems
- [ ] Build gallery functionality
- [ ] Implement video management system

### Phase 3: Frontend Implementation
- [ ] Develop responsive page templates
- [ ] Implement navigation and dropdowns
- [ ] Create dynamic content sections
- [ ] Build WhatsApp integration
- [ ] Create submission forms for testimonials, marksheets, and videos

### Phase 4: Testing & Deployment
- [ ] Test all features and responsive design
- [ ] Populate initial content
- [ ] Deploy to production server