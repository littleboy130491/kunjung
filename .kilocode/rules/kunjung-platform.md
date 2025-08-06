## Brief overview
This rule file provides guidelines for developing the Kunjung Platform, a Laravel-based accommodation booking platform focused on authentic travel experiences in Indonesia. It includes specific architectural choices, development workflows, and technology preferences based on comprehensive user research and product requirements.

## Communication style
- Be direct and technical in responses
- Avoid conversational starters like "Great", "Certainly", "Okay", "Sure"
- Focus on accomplishing tasks rather than engaging in back-and-forth dialogue
- Use precise technical terminology related to Laravel, Filament, Livewire, and Tailwind CSS

## Development workflow
- Follow the 3-phase development approach outlined in `DEVELOPMENT_TODO.md`:
  - Phase 1 (MVP): Foundation features and essential functionality
  - Phase 2 (Enhanced): User experience improvements
  - Phase 3 (Scale): Performance optimization and growth features
- Always update `DEVELOPMENT_TODO.md` immediately after completing any task:
  - Change `- [ ]` to `- [x]` for completed items
  - Add ✅ checkmark and brief completion notes
  - Update progress summary percentages
- Maintain accurate progress tracking in `CLAUDE_ACTIVITY_LOG.md` after each session

## Coding best practices
- Prioritize reputable third-party packages over custom development to accelerate progress
- Focus custom development time only on Kunjung-specific business logic:
  - Booking algorithms and pricing rules
  - Curated collections and local recommendations
  - Integration between third-party services
  - Custom Livewire components for reactive interfaces
- Follow Laravel conventions strictly for code organization
- Use Filament PHP for all admin panel interfaces
- Use Livewire for reactive frontend components
- Use Tailwind CSS 4.0 for styling with mobile-first responsive design
- Implement all media handling through Filament Curator (not Spatie MediaLibrary)
- Ensure all features align with the PRD user journey requirements

## Project context
- Project is built with Laravel 12 framework
- Core domain models include Properties, Users (guests/hosts/admins), Bookings, Reviews, and Local Guides
- Key integrations:
  - Midtrans for Indonesian payment methods
  - Maps API for location services
  - CDN for optimized image delivery
- Database uses SQLite with migrations for all core tables
- Admin panel built with Filament PHP with role-based access control via Filament Shield
- Frontend uses Tailwind CSS 4.0 and Livewire 3.0 for reactive components
- Testing framework is Pest PHP with comprehensive test coverage goals

## Other guidelines
- All development should reference `PRD_Kunjung_Platform.md` for feature requirements
- When implementing features, ensure alignment with the 5-stage user journey:
  - Awareness & Discovery
  - Consideration & Exploration
  - Booking & Conversion
  - Pre-Stay Preparation
  - Stay Experience & Post-Stay
- Follow the package selection criteria:
  - Active maintenance
  - Strong community
  - Laravel ecosystem compatibility
  - Quality documentation
  - Production readiness
- Ensure all code meets accessibility compliance (WCAG 2.1 AA standards)
- Implement security considerations in every feature
- Assess performance impact for each implementation
- Verify cross-browser compatibility (Chrome, Safari, Firefox)