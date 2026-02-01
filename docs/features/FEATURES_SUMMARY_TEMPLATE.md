# Feature: [Feature Name]

**Status:** 💡 Idea / 📋 Planned / 🚧 In Progress / ✅ Complete / ❌ Rejected  
**Priority:** 🔴 Critical / 🟡 High / 🟢 Medium / ⚪ Low  
**Estimated Effort:** S / M / L / XL  
**Target Version:** v1.1 / v2.0 / Future

---

## Problem

**What problem does this solve?**  
2-3 sentences describing the pain point or need. Be specific.

**Who needs this?**  
- Admin / Customer / Both
- Use case: [Describe scenario]

---

## Proposed Solution

**High-level approach:**  
Brief description of how this would work from a user perspective.

**Example Flow:**
1. User does X
2. System responds with Y
3. Result is Z

---

## Technical Overview

**What needs to change:**
- **Database:** New table `feature_items` with columns X, Y, Z
- **Models:** New `FeatureItem` model with relations to `User`, `Order`
- **Routes:** Add `GET /admin/features`, `POST /admin/features`
- **UI:** New admin page for managing features

**Dependencies:**
- Requires Task X to be complete
- Depends on existing `Order` model

**Estimated Complexity:**  
Simple / Moderate / Complex — [Why?]

---

## Benefits

**Value:**
- ✅ Saves admin 10 minutes per order
- ✅ Reduces customer confusion
- ✅ Improves data accuracy

**Metrics:**
- Expected usage: [X times per day/week]
- Impact: [High/Medium/Low]

---

## Risks & Considerations

**Potential Issues:**
- ⚠️ May slow down order page if not optimized
- ⚠️ Requires migration of existing data
- ⚠️ Adds complexity to order workflow

**Mitigation:**
- Use eager loading to prevent N+1 queries
- Create migration script with rollback plan
- Add clear UI indicators

---

## Out of Scope

**What this does NOT include:**
- ❌ Automatic email notifications (separate feature)
- ❌ Mobile app support (future consideration)
- ❌ Integration with external systems

---

## Decision

**Status:** Approved / Needs Discussion / Rejected  
**Decided by:** [Name/Role]  
**Date:** YYYY-MM-DD

**Reasoning:**  
Brief explanation of why this was approved/rejected.

**Next Steps:**
- [ ] Create detailed spec in `.kiro/specs/feature-name/`
- [ ] Break down into tasks
- [ ] Assign to milestone

---

## References

**Related:**
- Feature: [Related Feature Name]
- Task: [Related Task Number]
- Issue: [GitHub Issue #]

**Discussion:**
- [Link to discussion/meeting notes]

---

*Template Version: 1.0*
