# 🏗️ JimaCare Multi-App Setup Guide

## Overview

You'll have **two separate Flutter apps** that connect to the same Laravel backend:

1. **Provider App** - For care providers/carers
2. **Client App** - For clients who need care

Both apps will:
- Share the same Laravel backend API
- Work on Android and iOS
- Have different features based on user role
- Interact with each other through the backend

---

## Project Structure

```
flutter_app/
├── provider_app/          # Provider/Carer App
│   ├── lib/
│   ├── android/
│   ├── ios/
│   └── pubspec.yaml
│
├── client_app/            # Client App
│   ├── lib/
│   ├── android/
│   ├── ios/
│   └── pubspec.yaml
│
└── shared/                # Shared code (optional)
    ├── models/
    ├── services/
    └── widgets/
```

---

## App Differences

### Provider App (Carers)
- Browse available jobs
- Manage availability
- Accept/decline bookings
- Track location during jobs
- View earnings/analytics
- Manage profile & documents
- Video calls with clients

### Client App (Clients)
- Post care jobs
- Search for carers
- Book carers
- Track carer location
- Manage bookings
- Video calls with carers
- Payment management

---

## Next Steps

I'll create both apps for you now!

