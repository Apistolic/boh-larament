# Workflow Documentation

This document outlines the automated workflows in the system, organized by category.

## Donor Management Workflows

These workflows handle the donor lifecycle from initial contact through active donor status.

### New Donor Candidate Process

```mermaid
flowchart TD
    %% New Donor Candidate Process
    A[New Donor Contact Created/Updated] -->|Stage: donor_candidate| B[New Donor Candidate Welcome/Initial Outreach]
    B --> C[New Donor Confirmed?]
    C --> |Yes| D{{Continue with Donor Activation}}
    C --> |No| E[Send 2nd Outreach]
    E --> F[Send 3rd Outreach]
    F --> |Stage: donor_candidate_stagnant| G[Standard Donor Candidate Drip]
```

### New Donor Candidate Confirmed

```mermaid
flowchart TD
    %% New Donor Candidate Confirmed
    A[New Donor Confirmed] -->|Stage: donor_candidate_confirmed| B[Assign Development Team Member]
    B --> C[Schedule Initial Donor Meeting]
    C --> D[Initial Donor Meeting]
```

### Donor Activation/Renewal

```mermaid
flowchart TD
    %% Donor Activation/Renewal
    A[Donation Received] -->|donor_candidate -> donor| B[New Donor Milestone]
    C --> D[Donor Activation]
    C --> E[Send Thank You]
    C --> F[Add to Newsletter]
    C --> G[Schedule 30-day Followup]
    C --> H[Schedule 180-day Followups]
```

## Neighboring Volunteer Workflows

These workflows manage volunteer recruitment and onboarding processes.

### Neighboring Volunteer Process

```mermaid
flowchart TD
    %% Neighboring Volunteer Process
    A[Contact Created] -->|Stage: neighbor_candidate| B[New Volunteer Interest]
    B --> C[Send Info Packet]
    D[Receive Clearances] --> |Stage: neighbor_training_pending| E[Schedule Orientation]
    E --> F[Orientation]
    F -->|Stage: neighbor_active| G[Orientation Complete]
    G --> H[Assign Mentor]
```

### Volunteer Onboarding

```mermaid
flowchart TD
    %% Volunteer Onboarding
    E[Stage Changed] -->|neighbor_candidate -> neighbor| F[Volunteer Onboarding]
    F --> G[Send Welcome Kit]
    F --> H[Schedule Training]
    F --> I[Assign Mentor]
```

## Mom Program Workflows

These workflows handle the complete journey of program participants from application through graduation.

### Mom Program Flow

```mermaid
flowchart TD
    %% Mom Program Flow
    A[Contact Created] -->|Stage: mom_candidate| B[New Mom Application]
    B --> C[Send Application]
    B --> D[Create Review Task]
    B --> E[Schedule Interview]
```

### Mom Acceptance

```mermaid
flowchart TD
    %% Mom Acceptance
    F[Stage Changed] -->|mom_candidate -> mom_participant| G[Mom Program Acceptance]
    G --> H[Send Welcome Packet]
    G --> I[Assign Mentor]
    G --> J[Schedule Orientation]
```

### Mom Graduation

```mermaid
flowchart TD
    %% Mom Graduation
    K[Stage Changed] -->|mom_participant -> mom_graduate| L[Mom Program Graduation]
    L --> M[Send Congratulations]
    L --> N[Schedule Graduation]
    L --> O[Create Certificate]
    L --> P[Add to Alumni]
    L --> Q[Schedule 3-month Followup]
    L --> R[Update Program Stats]
```

## Gala Event Workflows

These workflows manage various aspects of the gala event, from invitations to auction management.

### Gala Invitation

```mermaid
flowchart TD
    %% Gala Workflows
    A[Manual Trigger] -->|Donor Level & Previous Attendance| B[Gala Invitation]
    B --> C[Send Invitation]
    B --> D[Create Followup Call]
```

### Gala Registration

```mermaid
flowchart TD
    %% Registration
    E[Contact Updated] -->|Registration Confirmed| F[Gala Registration]
    F --> G[Send Confirmation]
    F --> H[Add to Seating Chart]
    F --> I[Create Name Tag]
```

### Auction Winner

```mermaid
flowchart TD
    %% Auction Winner
    J[Manual Trigger] -->|Auction Won| K[Auction Winner Followup]
    K --> M[Process Payment]
    K --> L[Send Congratulations + 2 days]
```

### Neighboring Volunteer Gala Signup

```mermaid
flowchart TD
    %% Neighboring Volunteer Signup
    O[Contact Updated] -->|Volunteer Event: Gala| P[Gala Volunteer Signup]
    P --> Q[Send Instructions + 2 days]
```

### Workflow Trigger Types

Workflows can be initiated by several types of triggers:

1. **Contact Created** - Triggered when a new contact is added to the system
2. **Contact Updated** - Triggered when specific fields on a contact are modified
3. **Lifecycle Stage Changed** - Triggered when a contact moves from one lifecycle stage to another
4. **Manual** - Triggered by user action
5. **Time-based** - Triggered after a specific time period or at scheduled intervals

## Workflow Actions

Common actions performed by workflows include:

1. **Communication**
   - Send emails
   - Send welcome packets
   - Send congratulations
   
2. **Task Creation**
   - Schedule meetings
   - Create review tasks
   - Set up follow-ups
   
3. **Assignment**
   - Assign team members
   - Assign mentors
   
4. **Program Management**
   - Update contact stages
   - Add to groups/lists
   - Update program statistics
   
5. **Event Management**
   - Schedule orientations
   - Manage event registrations
   - Handle auction processes
